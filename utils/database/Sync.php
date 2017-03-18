<?php
class Sync extends Thread{



  public function run(){
    $general_ini=parse_ini_file("./settings/general.ini");
    $shared_db=new DBConnection("127.0.0.1","root","root",$general_ini["shared.db"],3306);
    $local_db=new DBConnection("127.0.0.1","root","root",$general_ini["local.db"],3306);

    while(true){
      $general_ini=parse_ini_file("./settings/general.ini");
      $my_fed=$general_ini["federation_name"];
      $sleep_time=$general_ini["sleep"];


      $query1=$local_db->query("select * from test_table where id_fd like '$my_fed' order by id desc limit 1");
      $query2=$shared_db->query("select * from test_table where id_fd like '$my_fed' order by remote_id desc limit 1");
      $query3=$local_db->query("select * from test_table where id_fd not like '$my_fed' order by id desc limit 1");
      $query4=$shared_db->query("select * from test_table where id_fd not like '$my_fed' order by id desc limit 1");
      $query5=$shared_db->query("select * from test_table where id_fd not like '$my_fed' and action = 1 order by id desc limit 1");


      $local_r1=mysqli_fetch_array($query1);
      $shared_r1=mysqli_fetch_array($query2);
      $local_r2=mysqli_fetch_array($query3);
      $shared_r2=mysqli_fetch_array($query4);

      echo "\n\n\n############### Cheking... ################";




      //using $query1 and $query2 here (UPLOADING)
      if(mysqli_num_rows($query2)==0){
        echo "\n\t>>TRYING TO POPULATE SHARED.DB FROM SKRATCH...";
        if(mysqli_num_rows($query1)>0){
          echo "\n\t\t>>POPULATING SHARED.DB";
          $this->upload_all($local_db,$shared_db,$my_fed);
        }else{
          echo "\n\t\t>>LOCAL.DB HAS NO DATA, NOT GOING TO POPULATE SHARED.DB";
        }
      }else if($local_r1["id"] > $shared_r1["remote_id"]){
        $this->upload_after_offset($shared_r1["remote_id"],$local_db,$shared_db,$my_fed);
      }else{
        echo "\n\tShared.db is up to date.";
      }




      //using $query3 and $query4 here (DOWNLOADING)
      if(mysqli_num_rows($query3)==0){
        echo "\n\t\t>>TRYING TO DOWNLOAD ALL FROM SHARED.DB...";
        if(mysqli_num_rows($query4)>0){
          echo "\n\t\t\tDOWNLOADING ALL FROM SHARED.DB...";
          $this->download_all($shared_db,$local_db,$my_fed);
        }else{
          echo "\n\t\tSHARED.DB HAS NOT DATA, NOT GOING TO DOWNLOAD ANYTHING";
        }
      }else if($shared_r2["id"] > $local_r2["shared_id"]){
        $this->download_after_offset($local_r2["shared_id"],$shared_db,$local_db,$my_fed);
        echo "\n\tMOST RECENT (NOT MINE) IN SHARED: ".$shared_r2["remote_id"].", ".$shared_r2["id_fd"];
        echo "\n\tMOST RECENT (NOT MINE) IN LOCAL: ".$local_r2["remote_id"].", ".$local_r2["id_fd"];
      }else{
        echo "\n\tLocal.db is up to date.";
      }

      $this->check_update_log($local_db,$shared_db,$my_fed);

      echo "\n############### SLEEP $sleep_time... ################";
      sleep($sleep_time);


    }

    $local_db->close();
    $shared_db->close();
    echo "\nLocal and Shared db connections closed";
  }

  private function flush_updates($db_left,$db_right,$my_fed){
    $str="select * from test_table where action=1 and id_fd not like '$my_fed'";
    $query=$db_right->query($str);
    while($row=mysqli_fetch_array($query)){
      $str2="delete from test_table where id_fd like '".$row["id_fd"]."' and remote_id=".$row["remote_id"];
      $query2=$db_left->query($str2);
      $str3="insert into test_table(time,id_fd,remote_id,shared_id) "
            ."values(".$row["time"].",'".$row["id_fd"]."',".$row["remote_id"].",".$row["id"].")";
      $db_left->query($str3);
      echo "\n\t\t>>ROW ID: ".$row["remote_id"]." (".$row["id_fd"].")";
    }
  }


  private function update_after_offset($offset_left,$db_left,$db_right,$my_fed){
    $str="select * from update_log where id > $offset_left";
    $query=$db_left->query($str);
    while($row=mysqli_fetch_array($query)){
      $db_left->query("insert into tmp_update_log(id,local_id) values(".$row["id"].",".$row["local_id"].")");
      $str2="select * from test_table where id = ".$row["local_id"];
      $query2=$db_left->query($str2);
      $u=mysqli_fetch_array($query2);
      $db_right->query("delete from test_table where id_fd like '$my_fed' and remote_id = ".$u["id"]);
      $db_right->query("insert into test_table(time,remote_id,id_fd,action) values(".$u["time"].",".$u["id"].",'".$u["id_fd"]."',1)");
      echo "\n\t\t\t>>ROW ID: ".$u["id"];
    }
  }

  private function update_all($db_left,$db_right,$my_fed){
    $this->update_after_offset(0,$db_left,$db_right,$my_fed);
  }

  //uploads data from left database (starting from row $offset_left) to right database
  private function upload_after_offset($offset_left,$db_left,$db_right,$my_fed){
      $str="select * from test_table where id > $offset_left and id_fd like '$my_fed'";
      $result=$db_left->query($str);
      while($row=mysqli_fetch_array($result)){
        $str="insert into test_table(time,remote_id,id_fd) values (".$row["time"].",".$row["id"].",'$my_fed')";
        $db_right->query($str);
        echo "\n\t>>ROW ID ".$row["id"]." UPLOADED";
      }
  }

  //uploads data from left database to right database
  private function upload_all($db_left,$db_right,$my_fed){
    $this->upload_after_offset(0,$db_left,$db_right,$my_fed);
  }


  private function download_after_offset($offset,$db_left,$db_right,$my_fed){
    $query=$db_left->query("select * from test_table where id_fd not like '$my_fed' AND id > $offset");
    while($row=mysqli_fetch_array($query)){
      //if it's an update...
      if($row["action"]==1){
        //...delete the previews version of this row from db_right, and insert this current new one into db_right
        //note: db_right is probably local.db
        $string="delete from test_table where id_fd like '".$row["id_fd"]."' and remote_id = ".$row["remote_id"];
        $db_right->query($string);
      }
      echo "\n\t\t\t>>ROW ID: ".$row["id"]." DOWNLOADED";
      $string="insert into test_table(time,id_fd,remote_id,shared_id) values(".$row["time"].",'".$row["id_fd"]."',".$row["remote_id"].",".$row["id"].");";
      echo "\n\t\t\t\t$string";
      $db_right->query($string);
    }
  }

  private function download_all($db_left,$db_right,$my_fed){
    $this->download_after_offset(0,$db_left,$db_right,$my_fed);
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


  //IMPORTANT
  /*
    [MY SERVER]
    check_update_log() iterates through tables tmp_update_log and update_log, compares them,
    and if update_log is ahead of tmp_update_log, it will start copying the missing data
    from update_log to tmp_update_log, while doing so, for each row, it will push
    updates to shared.db and at the same time the old rows from shared.db will be
    deleted. It simulates and update on the shared.db.

    [OTHER SERVERS]
    When other servers will try to download something new from the shared.db
    They will see the flag "1" on the action attribute on the rows,
    they will automatically get the row, and delete from their local.db
    rvery row with that same remote_id and same id_fd, doing so they are
    basically deleting every trace of that particular remote_id,
    then, they will make a new insert into the local.db, using the new data
    that has been fetched from shared.db.
    This whole process is implemented in the download_after_offset() method. Check it.
  */
  private function check_update_log($db_left,$db_right,$my_fed){
    $result=array();
    //saving the last row of the temporary table (tmp_update_log) into an array
    $tmp_last_update=$this->getLastTmpUpdateLog($db_left);


    //fetching the last row from the actual update_log table
    $last_update=$this->getLastUpdateLog($db_left);




    echo "\n\n\n\t[TMP_UPDATE_LOG: "
      .(is_null($tmp_last_update["id"])?null:$tmp_last_update["id"])
      ."] - [UPDATE_LOG: "
      .(is_null($last_update["id"])?null:$last_update["id"])
      ."]";

      if($tmp_last_update==null){
        if($last_update!=null){
          echo "\n\t\t>>Updating all...";
          $this->update_all($db_left,$db_right,$my_fed);
        }
      }else if($last_update["id"] > $tmp_last_update["id"]){
          echo "\n\t\t>>Updating from offset: ".$tmp_last_update["id"];
          $this->update_after_offset($tmp_last_update["id"],$db_left,$db_right,$my_fed);
      }

  }
}
