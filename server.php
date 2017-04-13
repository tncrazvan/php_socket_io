<?php
require_once("./utils/readers/ServerReader64.php");
require_once("./utils/database/Sync.php");


$sync = new class extends Thread{
	public function run(){
    $general_ini=parse_ini_file("./settings/general.ini");
    $local_db=new DBConnection($general_ini["local_address"],"root","root",$general_ini["local_database_name"],3306);
    $shared_db=new DBConnection($general_ini["shared_address"],"root","root",$general_ini["shared_database_name"],3306);

    $my_fed=$general_ini["federation_name"];
    $sleep_time=$general_ini["sleep_time"];

    while(true){

      $query1=$local_db->query("select * from general where id_fd like '$my_fed' order by remote_id desc limit 1");
      $query2=$shared_db->query("select * from general where id_fd like '$my_fed' order by remote_id desc limit 1");

      $query3=$local_db->query("select * from general where id_fd not like '$my_fed' order by id desc limit 1");
      $query4=$shared_db->query("select * from general where id_fd not like '$my_fed' order by id desc limit 1");


      $local_r1=mysqli_fetch_array($query1);
      $shared_r1=mysqli_fetch_array($query2);

      $local_r2=mysqli_fetch_array($query3);
      $shared_r2=mysqli_fetch_array($query4);

      echo "\n\n\n\n\n\n\n############### Cheking... ################";

      //using $query1 and $query2 here (UPLOADING)
      if(mysqli_num_rows($query2)==0){
        if(mysqli_num_rows($query1)>0){
          echo "\n\t>>Trying to upload everything to the shared database...";
          Sync::upload_all($local_db,$shared_db,$my_fed);
        }else{
          echo "\n\tShared database is up to date.";
        }
      }else if($local_r1["id"] > $shared_r1["remote_id"]){
        echo "\n\t>>Uploading...";
        Sync::upload_after_offset($shared_r1["remote_id"],$local_db,$shared_db,$my_fed);
      }else{
        echo "\n\tShared database is up to date.";
      }




      //using $query3 and $query4 here (DOWNLOADING)
      if(mysqli_num_rows($query3)==0){
        if(mysqli_num_rows($query4)>0){
          echo "\n\t<<Downloading everything from the shared database.";
          Sync::download_all($shared_db,$local_db,$my_fed);
        }else{
          echo "\n\tLocal.db is up to date.";
        }
      }else if($shared_r2["id"] > $local_r2["shared_id"]){
        echo "\n\t<<Downloading...";
        Sync::download_after_offset($local_r2["shared_id"],$shared_db,$local_db,$my_fed);
      }else{
        echo "\n\tLocal.db is up to date.";
      }


      //fetching the last row from the actual update_log table
      $last_update=Sync::getLastUpdateLog($local_db);

      if($last_update!=null){
        echo "\n\t>>Updating everything...";
        Sync::update_all($local_db,$shared_db,$my_fed);
      }else{
        echo "\n\tNo updates available.";
      }


      echo "\n############### SLEEP $sleep_time... ################";
      sleep($sleep_time);


    }

    $local_db->close();
    $shared_db->close();
    echo "\nLocal and Shared db connections closed";
  }
};

$sync->start() && $sync->join();


/*
	$allowed_to_run is a flag which allows the script to loop and accept client connection_status.
	assigning "false" makes it so the server stops listening for connections.
	IMPORTANT: already enstablished connections will not be lost.
*/
$allowed_to_run=true;
//creo un nuovo Thread
if(!($socket=socket_create(AF_INET, SOCK_STREAM,SOL_TCP))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);

	die("\nCouldn't create socket: [$errorcode] $errormsg");
}

echo "\nSocket created";

/*
	Bind of Source Address.
	This server prepeares its socket for incoming connections.
*/

if(!socket_bind($socket,"127.0.0.1",5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nCould not bind socket: [$errorcode] $errormsg");
}

echo "\nSocked bind OK";

/*
	Sarting to listen for connections (not blocking call)
*/
if(!socket_listen($socket,5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket can't listen:  [$errorcode] $errormsg");
}

while($allowed_to_run){
	//accepting 1 incoming connection
	$client=socket_accept($socket);
	/*
		ServerReader64 creates a Reader64, which in turn creates a Thread
		which will read the client's message in chunks of 2048 bytes
	*/
	$sr=new ServerReader64($client/*client's socket*/,2048/*MTU*/);

	//starting the reader (which is a thread)
	$sr->start();
}


//closing my(server) socket
socket_shutdown($socket);
socket_close($socket);
