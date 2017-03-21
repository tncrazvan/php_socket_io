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
        echo "\n\n\t>>TRYING TO DOWNLOAD ALL FROM SHARED.DB...";
        if(mysqli_num_rows($query4)>0){
          echo "\n\t\t>>DOWNLOADING ALL FROM SHARED.DB";
          $this->download_all($shared_db,$local_db,$my_fed);
        }else{
          echo "\n\t\tSHARED.DB HAS NOT DATA, NOT GOING TO DOWNLOAD ANYTHING";
        }
      }else if($shared_r2["id"] > $local_r2["shared_id"]){
        $this->download_after_offset($local_r2["shared_id"],$shared_db,$local_db,$my_fed);
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


  private function update_after_offset($offset_left,$db_left,$db_right,$my_fed){
    $str="select * from update_log where id > $offset_left";
    $query=$db_left->query($str);
    while($row=mysqli_fetch_array($query)){
      /*
        checking the id_fd of the current row
      */
      $r=mysqli_fetch_array($db_left->query("select id_fd,status  from test_table where id=".$row["local_id"]));

      //checking if the id_fd is mine or ifit's draft, if it is,
      //I can update it, otherwise, delete the update attempt from my local db
      //and skip this row update (IN SHORT: don't bother to query to the shared.db)
      if($r["id_fd"]!=$my_fed || $r["status"]=="draft"){
        $db_left->query("delete from update_log where id=".$row["id"]);
      }else{
        //else update
        $db_left->query("insert into tmp_update_log(id,local_id) values(".$row["id"].",".$row["local_id"].")");
        $str2="select * from test_table where id = ".$row["local_id"];
        $query2=$db_left->query($str2);
        $u=mysqli_fetch_array($query2);
        $db_right->query("delete from test_table where id_fd like '$my_fed' and remote_id = ".$u["id"]);
        $db_right->query("insert into test_table(title,remote_id,id_fd,status) "
                        ."values('".$u["title"]."',".$u["id"].",'".$u["id_fd"]."','".$u["status"]."')");
        echo "\n\t\t\t>>ROW ID: ".$u["id"];
      }
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
        if($row["status"]!='draft'){
          $str="insert into test_table(title,remote_id,id_fd,status) "
              ."values ('".$row["title"]."',".$row["id"].",'$my_fed','".$row["status"]."')";
          $db_right->query($str);
          echo "\n\t\t\t>>ROW ID ".$row["id"]." UPLOADED";
        }
      }
  }

  //uploads data from left database to right database
  private function upload_all($db_left,$db_right,$my_fed){
    $this->upload_after_offset(0,$db_left,$db_right,$my_fed);
  }


  private function download_after_offset($offset,$db_left/*shared*/,$db_right/*local*/,$my_fed){
    $query=$db_left->query("select * from test_table where id_fd not like '$my_fed' AND id > $offset");
    while($row=mysqli_fetch_array($query)){
      //deleting the entry if already exists and writing a new one using the same IDs and updated data (simulates an update query)
      $db_right->query("delete from test_table where id_fd like '".$row["id_fd"]."' and remote_id=".$row["remote_id"]);

      echo "\n\t\t\t>>ROW ID: ".$row["id"]." DOWNLOADED";
      $string="insert into test_table(title,id_fd,remote_id,shared_id,status) "
              ."values('".$row["title"]."','".$row["id_fd"]."',".$row["remote_id"].",".$row["id"].",'".$row["status"]."');";
      //echo "\n\t\t\t\t$string";
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


  private function check_update_log($db_left,$db_right,$my_fed){

    //saving the last row of the temporary table (tmp_update_log) into an array
    $tmp_last_update=$this->getLastTmpUpdateLog($db_left);


    //fetching the last row from the actual update_log table
    $last_update=$this->getLastUpdateLog($db_left);




    /*echo "\n\n\n\t[TMP_UPDATE_LOG: "
      .(is_null($tmp_last_update["id"])?null:$tmp_last_update["id"])
      ."] - [UPDATE_LOG: "
      .(is_null($last_update["id"])?null:$last_update["id"])
      ."]";*/

      if($tmp_last_update==null){
        if($last_update!=null){
          echo "\n\n\t>>Updating all...";
          $this->update_all($db_left,$db_right,$my_fed);
        }else{
          echo "\n\t>>No updates available";
        }
      }else if($last_update["id"] > $tmp_last_update["id"]){
          echo "\n\n\t>>Updating after offset: ".$tmp_last_update["id"];
          $this->update_after_offset($tmp_last_update["id"],$db_left,$db_right,$my_fed);
      }else{
        echo "\n\n\t>>No updates available";
      }

  }

}
