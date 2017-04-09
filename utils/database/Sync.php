<?php
class Sync extends Thread{


  public function run(){
    $general_ini=parse_ini_file("./settings/general.ini");
    $local_db=new DBConnection("127.0.0.1","root","root",$general_ini["local_database_name"],3306);
    $shared_db=new DBConnection("127.0.0.1","root","root",$general_ini["shared_database_name"],3306);

    $my_fed=$general_ini["federation_name"];
    $sleep_time=$general_ini["sleep"];

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
          $this->upload_all($local_db,$shared_db,$my_fed);
        }else{
          echo "\n\tShared database is up to date.";
        }
      }else if($local_r1["id"] > $shared_r1["remote_id"]){
        $this->upload_after_offset($shared_r1["remote_id"],$local_db,$shared_db,$my_fed);
      }else{
        echo "\n\tShared database is up to date.";
      }




      //using $query3 and $query4 here (DOWNLOADING)
      if(mysqli_num_rows($query3)==0){
        if(mysqli_num_rows($query4)>0){
          echo "\n\t<<Downloading everything from the shared database.";
          $this->download_all($shared_db,$local_db,$my_fed);
        }else{
          echo "\n\tLocal.db is up to date.";
        }
      }else if($shared_r2["id"] > $local_r2["shared_id"]){
        //$this->download_after_offset($local_r2["shared_id"],$shared_db,$local_db,$my_fed);
      }else{
        echo "\n\tLocal.db is up to date.";
      }



      $this->check_update_log($local_db,$shared_db,$my_fed);

      //$this->check_delete_log($local_db,$shared_db,$my_fed);

      echo "\n############### SLEEP $sleep_time... ################";
      sleep($sleep_time);


    }

    $local_db->close();
    $shared_db->close();
    echo "\nLocal and Shared db connections closed";
  }


  private function update_after_offset($offset_local,$local_db,$shared_db,$my_fed){
    $str="select * from update_log where id > $offset_local";
    $query=$local_db->query($str);
    while($row=mysqli_fetch_array($query)){
      /*
        checking if the current row (which will be updated) is a draft, if it is,
        delete the commit and don't send it to the shared.db, but keep the row in local.db
      */
      $tmp_r = $local_db->query("select status from general where id_fd like '$my_fed' and id=".$row["local_id"]);
      $r=mysqli_fetch_array($tmp_r);
      if($r["status"]=="draft"){
        $statement = $local_db->prepare("delete from update_log where id=?");
        $statement->bind_param("i",$row["id"]);
        $statement->execute();
        $statement->close();
      }else{
        //else update

        //updateing tmp_log_update with update_log
        $statement = $local_db->prepare("insert into tmp_update_log(id,local_id) values(?,?)");
        $statement->bind_param("ii",$row["id"],$row["local_id"]);
        if ($statement->execute() == false) echo "\n\t\tERROR: ".$statement->error;
        $statement->close();

        //fetching the row from general
        $result=$local_db->query("select * from general where id_fd like '$my_fed' and id = ".$row["local_id"]);

        $tmp_row=mysqli_fetch_array($result);

        /*
          This will make sure that if an article with the same id_fd and the same id already exists
          in the shared.db, it will be deleted before inserting the new, updated, version of that article.
          This avoid duplicate entries and simulates and update query, and since the id of the row itself
          is auto_incremented, this means that every daemon listening to the given shared.db will be notified
          when the next routine starts, and they will download the new updated, article.
          The other daemons must make sure they also delete the old version of the entry bofore inserting the new one.
        */


        $statement = $shared_db->prepare("delete from general where id_fd like ? and remote_id = ?");
        $statement->bind_param("si",$my_fed,$tmp_row["id"]);
        if ($statement->execute() == false) echo "\n\t\tERROR: ".$statement->error;
        $statement->close();


        /*
          Now that I'm sure there are no duplicates,
          I can upload the entry to the shared.db
        */
        $str="insert into general(
            id_fd,remote_id,
            status,title,language,
            description,keyword,coverage,
            structure,aggregation_level
          ) "
            ."values (?,?,?,?,?,?,?,?,?,?)";
        $statement = $shared_db->prepare($str);
        $statement->bind_param("sisssssssi",
          $tmp_row["id_fd"],$tmp_row["id"],
          $tmp_row["status"],$tmp_row["title"],$tmp_row["language"],
          $tmp_row["description"],$tmp_row["keyword"],$tmp_row["coverage"],
          $tmp_row["structure"],$tmp_row["aggregation_level"]
        );
        $statement->execute();
        $statement->close();

        /*
          there's actually no need to insert it right away,
          the other daemons can download the newest version durin the next sync routine
        */

        echo "\n\t\t>>Row ".$tmp_row["id"]." has been updated.";
      }
    }
  }

  private function update_all($local_db,$shared_db,$my_fed){
    $this->update_after_offset(0,$local_db,$shared_db,$my_fed);
  }


  //uploads data from left database (starting from row $offset_local) to right database
  private function upload_after_offset($offset_local,$local_db,$shared_db,$my_fed){
      $str="select * from general where id > $offset_local";
      $result=$local_db->query($str);

      while($row=mysqli_fetch_array($result)){
        if($row["status"]!='draft'){
          $str="insert into general(
              id_fd,remote_id,
              status,title,language,
              description,keyword,coverage,
              structure,aggregation_level
            ) "
              ."values (?,?,?,?,?,?,?,?,?,?)";
          $statement = $shared_db->prepare($str);
          $statement->bind_param("sisssssssi",
            $row["id_fd"],$row["id"],
            $row["status"],$row["title"],$row["language"],
            $row["description"],$row["keyword"],$row["coverage"],
            $row["structure"],$row["aggregation_level"]
          );
          $statement->execute();
          $statement->close();
          echo "\n\t\t>>Row ".$row["id"]." has been uploaded.";
        }
      }
  }

  //uploads data from left database to right database
  private function upload_all($local_db,$shared_db,$my_fed){
    $this->upload_after_offset(0,$local_db,$shared_db,$my_fed);
  }


  private function download_after_offset($offset_shared,$shared_db,$local_db,$my_fed){
    $query=$shared_db->query("select * from general where id_fd not like '$my_fed' AND id > $offset_shared");
    while($row=mysqli_fetch_array($query)){
      /*
        Deleting duplicate entries and writing a new one using the
        same IDs but with updated data (simulates an update query)
      */
      $statement=$local_db->prepare("delete from general where id_fd like ? and remote_id = ?");
      $statement->bind_param("si",$my_fed,$row["remote_id"]);
      if($statement->execute() == false) echo "\n\t\tERROR:".$statement->error;
      $statement->close();


      $str="insert into general(
          id_fd,remote_id,shared_id,
          status,title,language,
          description,keyword,coverage,
          structure,aggregation_level
        ) "
          ."values (?,?,?,?,?,?,?,?,?,?,?)";

      $statement = $local_db->prepare($str);
      $statement->bind_param("siisssssssi",
        $row["id_fd"],$row["remote_id"],$row["id"],
        $row["status"],$row["title"],$row["language"],
        $row["description"],$row["keyword"],$row["coverage"],
        $row["structure"],$row["aggregation_level"]
      );
      if($statement->execute() == false) echo "\n\t\tERROR: ".$statement->error;
      $statement->close();

      echo "\n\t\t<<Row ".$row["id"]." has been downloaded.";
    }
  }

  private function download_all($shared_db,$local_db,$my_fed){
    $this->download_after_offset(0,$shared_db,$local_db,$my_fed);
  }

  private function getLastTmpUpdateLog($local_db){
    $tmp_str="select * from tmp_update_log order by id desc limit 1";
    $tmp_query=$local_db->query($tmp_str);

    if(mysqli_num_rows($tmp_query)==0){
      return null;
    }else{
      return mysqli_fetch_array($tmp_query);
    }
  }

  private function getLastUpdateLog($local_db){
    $str="select * from update_log order by id desc limit 1";
    $query=$local_db->query($str);

    if(mysqli_num_rows($query)==0){
      return null;
    }else{
      return mysqli_fetch_array($query);
    }
  }


  private function check_update_log($local_db,$shared_db,$my_fed){

    //saving the last row of the temporary table (tmp_update_log) into an array
    $tmp_last_update=$this->getLastTmpUpdateLog($local_db);


    //fetching the last row from the actual update_log table
    $last_update=$this->getLastUpdateLog($local_db);




    /*echo "\n\n\n\t[TMP_UPDATE_LOG: "
      .(is_null($tmp_last_update["id"])?null:$tmp_last_update["id"])
      ."] - [UPDATE_LOG: "
      .(is_null($last_update["id"])?null:$last_update["id"])
      ."]";*/

      if($tmp_last_update==null){
        if($last_update!=null){
          echo "\n\n\t>>Updating everything...";
          $this->update_all($local_db,$shared_db,$my_fed);
        }else{
          echo "\n\tNo updates available.";
        }
      }else if($last_update["id"] > $tmp_last_update["id"]){
          echo "\n\n\t>>Updating after offset ".$tmp_last_update["id"];
          $this->update_after_offset($tmp_last_update["id"],$local_db,$shared_db,$my_fed);
      }else{
        echo "\n\n\tNo updates available.";
      }

  }

}
