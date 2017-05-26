<?php
require_once(WORKSPACE."./utils/readers/Reader64.php");
require_once(WORKSPACE."./utils/database/DBConnection.php");
require_once(WORKSPACE."./utils/messages/RoutineMessage64.php");
require_once(WORKSPACE."./utils/database/Sync.php");
class ServerReader64 extends Reader64{
  function ServerReader($sender_socket,$bytes=1){
    //Instantiating the object and calling its parent's constructor "Reader" (which is a Thread)
    parent::Reader64($sender_socket,$bytes);
  }

  protected function callback($result,$address,$port){
    //callback code

    /*
      converting json result into a php Array.
      "true" attribute says the result is not an Object but an Array instead.
    */
    $data=json_decode($result,true);

    print("\n\t\tContent type: ".$data["content-type"]);
    switch($data["content-type"]){
      case "image-jpg":
        print("\n\t\tFile-short-name: \"".$data["file-short-name"]."\"");
        print("\n\t\tFile-extension: \"".$data["file-extension"]."\"");
        file_put_contents("./res/".$data["file-short-name"].".".$data["file-extension"], base64_decode($data["content64"]));
      break;

      case "text-plain":
        echo "\n\t\tCLIENT SAYS: ".$data["content64"];
      break;

      case "routine-request":
        print("\n\t\tRoutine type: ".$data["routine-type"]);
        $general_ini=parse_ini_file(WORKSPACE."./settings/general.ini");

        $local_db=new DBConnection("localhost",$general_ini);
        $shared_db=new DBConnection("sharedhost",$general_ini);

        $my_fed=$general_ini["federation_name"];

        switch ($data["routine-type"]) {
          case 'download':
            $query3=$local_db->query("select * from lo_general where id_fd not like '$my_fed' order by id desc limit 1");
            $query4=$shared_db->query("select * from lo_general where id_fd not like '$my_fed' order by id desc limit 1");

            $local_r2=mysqli_fetch_array($query3);
            $shared_r2=mysqli_fetch_array($query4);

            if(mysqli_num_rows($query3)==0){
              if(mysqli_num_rows($query4)>0){
                echo "\n\t\t<<Downloading everything from the shared database (limit ".$data["limit"].").";
                Sync::download_all($shared_db,$local_db,$my_fed,$data["limit"]);
              }else{
                echo "\n\t\tLocal.db is up to date.";
              }
            }else if($shared_r2["id"] > $local_r2["shared_id"]){
              echo "\n\t\t<<Downloading (limit ".$data["limit"].")...";
              Sync::download_after_offset($local_r2["shared_id"],$shared_db,$local_db,$my_fed,$data["limit"]);
            }else{
              echo "\n\t\tLocal.db is up to date.";
            }

            break;
          case 'upload':
            $query1=$local_db->query("select * from lo_general as G left join lo_lifecycle as L using(Id_Lo,Id_Fd) where G.Id_Fd like '$my_fed' order by G.Id_Lo desc limit 1;");
            $query2=$shared_db->query("select * from lo_general where id_fd like '$my_fed' order by id_lo desc limit 1");

            $local_r1=mysqli_fetch_array($query1);
            $shared_r1=mysqli_fetch_array($query2);

            if(mysqli_num_rows($query2)==0){
              if(mysqli_num_rows($query1)>0){
                echo "\n\t\t>>Trying to upload everything (limit ".$data["limit"].")...";
                Sync::upload_all($local_db,$shared_db,$my_fed,$data["limit"]);
              }else{
                echo "\n\t\tShared database is up to date.";
              }
            }else if($local_r1["Id_Lo"] > $shared_r1["Id_Lo"] && $local_r1["Status"]!="draft"){
              echo "\n\t\t>>Uploading (limit ".$data["limit"].")...";
              Sync::upload_after_offset($shared_r1["Id_Lo"],$local_db,$shared_db,$my_fed,$data["limit"]);
            }else{
              echo "\n\t\tShared database is up to date.";
            }
            break;
          case 'update':
            $last_update=Sync::get_last_update_log($local_db);

            if($last_update!=null){
              echo "\n\t\t>>Updating everything (limit ".$data["limit"].")...";
              Sync::update_all($local_db,$shared_db,$my_fed,$data["limit"]);
            }else{
              echo "\n\t\tNo updates available.";
            }
            break;
        }

      break;
    }


  }
}
