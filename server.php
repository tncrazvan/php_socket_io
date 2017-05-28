<?php
//let this script run forever
set_time_limit(0);
//give this script infinite ammount of memory
ini_set("memory_imit",-1);

define("WORKSPACE",__DIR__."/");
define("LOG_FILE",WORKSPACE."utils/logs/".time().".log");
if(!file_exists(WORKSPACE."utils/logs")){
	mkdir(WORKSPACE."utils/logs" ,0777);
}
require_once(WORKSPACE."./utils/readers/ServerReader64.php");
require_once(WORKSPACE."./utils/database/Sync.php");
require_once(WORKSPACE."./utils/writers/Logger.php");

//parse settings file
$general_ini=parse_ini_file(WORKSPACE."./settings/general.ini");

$sync = new class extends Thread{
	public function run(){


		$general_ini=parse_ini_file(WORKSPACE."./settings/general.ini");
    $local_db=new DBConnection("localhost",$general_ini);
    $shared_db=new DBConnection("sharedhost",$general_ini);

    $my_fed=$general_ini["federation_name"];
    $sleep_time=$general_ini["sleep_time"];


		$result = $shared_db->query("select * from `lo_federation` where `ServerName` like '$my_fed'");
		if(mysqli_num_rows($result)<1){
			$statement = $shared_db->prepare("insert into `lo_federation`(ServerName,ServerAddress,TimeUpd) value(?,?,?)");
			$time_tmp=time();
			$statement->bind_param("ssi",$my_fed,$general_ini["local_address"],$time_tmp);
			$statement->execute();
			Logger::put("\nFederation \"$my_fed\" has been signed up to the comunity.");
			$statement->close();
		}else{
			Logger::put("\nFederation \"$my_fed\" is already signed up. Skipping registration.");
		}
    while(true){
			$general_ini = parse_ini_file(WORKSPACE."./settings/general.ini");
			$my_fed=$general_ini["federation_name"];
	    $sleep_time=$general_ini["sleep_time"];

      $query1=$local_db->query("select * from lo_general as G left join lo_lifecycle as L using(Id_Lo,Id_Fd) where G.Id_Fd like '$my_fed' order by G.Id_Lo desc limit 1;");
      $query2=$shared_db->query("select * from lo_general where id_fd like '$my_fed' order by id_lo desc limit 1");

      $query3=$local_db->query("select * from lo_general where id_fd not like '$my_fed' order by id desc limit 1");
      $query4=$shared_db->query("select * from lo_general where id_fd not like '$my_fed' order by id desc limit 1");


      $local_r1=mysqli_fetch_array($query1);
      $shared_r1=mysqli_fetch_array($query2);

      $local_r2=mysqli_fetch_array($query3);
      $shared_r2=mysqli_fetch_array($query4);

      Logger::put("\n\n\n\n\n\n\n############### Cheking... ################");
      Logger::put("\nCurrent time: ".date("r",time()));

			Logger::put("\n\tFederate: $my_fed");
      //using $query1 and $query2 here (UPLOADING)
      if(mysqli_num_rows($query2)==0){
        if(mysqli_num_rows($query1)>0){
          Logger::put("\n\t>>Trying to upload everything (limit ".$general_ini["upload_limit"].")...");
          Sync::upload_all($local_db,$shared_db,$my_fed,$general_ini["upload_limit"]);
        }else{
          Logger::put("\n\tShared database is up to date.");
        }
      }else if($local_r1["Id_Lo"] > $shared_r1["Id_Lo"] && $local_r1["Status"]!="draft"){
        Logger::put("\n\t>>Uploading (limit ".$general_ini["upload_limit"].")...");
        Sync::upload_after_offset($shared_r1["Id_Lo"],$local_db,$shared_db,$my_fed,$general_ini["upload_limit"]);
      }else{
        Logger::put("\n\tShared database is up to date.");
      }



      //using $query3 and $query4 here (DOWNLOADING)
      if(mysqli_num_rows($query3)==0){
        if(mysqli_num_rows($query4)>0){
          Logger::put("\n\t<<Downloading everything from the shared database (limit ".$general_ini["download_limit"].").");
          Sync::download_all($shared_db,$local_db,$my_fed,$general_ini["download_limit"]);
        }else{
          Logger::put("\n\tLocal.db is up to date.");
        }
      }else if($shared_r2["id"] > $local_r2["shared_id"]){
        Logger::put("\n\t<<Downloading (limit ".$general_ini["download_limit"].")...");
        Sync::download_after_offset($local_r2["shared_id"],$shared_db,$local_db,$my_fed,$general_ini["download_limit"]);
      }else{
        Logger::put("\n\tLocal.db is up to date.");
      }


      //fetching the last row from the actual update_log table
      $last_update=Sync::get_last_update_log($local_db);

      if($last_update!=null){
        Logger::put("\n\t>>Updating everything (limit ".$general_ini["update_limit"].")...");
        Sync::update_all($local_db,$shared_db,$my_fed,$general_ini["update_limit"]);
      }else{
        Logger::put("\n\tNo updates available.");
      }


      Logger::put("\n############### SLEEP $sleep_time... ################");
      sleep($sleep_time);


    }

    $local_db->close();
    $shared_db->close();
    Logger::put("\nLocal and Shared db connections closed");
  }
};

$sync->start();


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

Logger::put("\nSocket created");

/*
	Bind of Source Address.
	This server prepeares its socket for incoming connections.
*/

if(!socket_bind($socket,"127.0.0.1",$general_ini["listener_port"])){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nCould not bind socket: [$errorcode] $errormsg");
}

Logger::put("\nSocked bind OK");

/*
	Sarting to listen for connections (not blocking call)
*/
if(!socket_listen($socket,$general_ini["listener_port"])){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	Logger::put("\nSocket can't listen:  [$errorcode] $errormsg");
}

while($allowed_to_run){
	//accepting 1 incoming connection
	$client=socket_accept($socket);


	$general_ini=parse_ini_file(WORKSPACE."./settings/general.ini");
	/*
		ServerReader64 creates a Reader64, which in turn creates a Thread
		which will read the client's message in chunks of 2048 bytes
	*/
	$sr=new ServerReader64($client/*client's socket*/,$general_ini["server_reader_mtu"]/*MTU*/);

	//starting the reader (which is a thread)
	$sr->start();
}


//closing my(server) socket
socket_shutdown($socket);
socket_close($socket);
