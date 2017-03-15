<?php
class Sync extends Thread{



  public function run(){
    while(true){
      $general_ini=parse_ini_file("./settings/general.ini");
      $my_fed=$general_ini["federation_name"];
      $sleep_time=$general_ini["sleep"];

      $shared_db=new DBConnection("127.0.0.1","root","root","shared_test",3306);
      $local_db=new DBConnection("127.0.0.1","root","root","test",3306);


      $query1=$local_db->query("select * from test_table where id_fd like '$my_fed' order by id desc limit 1");
      $query2=$shared_db->query("select * from test_table where id_fd like '$my_fed' order by id desc limit 1");
      $query3=$local_db->query("select * from test_table where id_fd not like '$my_fed' order by id desc limit 1");
      $query4=$shared_db->query("select * from test_table where id_fd not like '$my_fed' order by id desc limit 1");

      $local_r1=mysqli_fetch_array($query1);
      $shared_r1=mysqli_fetch_array($query2);
      $local_r2=mysqli_fetch_array($query3);
      $shared_r2=mysqli_fetch_array($query4);


      echo "\n\n\n############### Cheking... ################";
      //using $query1 and $query2 here (UPLOADING)
      if(mysqli_num_rows($query2)==0){
        echo "\n\t>> TRYING TO POPULATE SHARED.DB FROM SKRATCH...";
        if(mysqli_num_rows($query1)>0){
          echo "\n\t\t>> POPULATING SHARED.DB";
          $this->upload_all($local_db,$shared_db,$my_fed);
        }else{
          echo "\n\t\t>> LOCAL.DB HAS NO DATA, NOT GOING TO POPULATE SHARED.DB";
        }
      }else if($local_r1["id"] > $shared_r1["remote_id"]){
          $this->upload_after_offset($shared_r1["remote_id"],$local_db,$shared_db,$my_fed);
      }else{
        echo "\n\t Shared.db is up to date.";
      }


      //using $query3 and $query4 here (DOWNLOADING)

      echo "\n\tMOST RECENT (NOT MINE) IN SHARED: ".$shared_r2["remote_id"].", ".$shared_r2["id_fd"];
      echo "\n\tMOST RECENT (NOT MINE) IN LOCAL: ";

      if(mysqli_num_rows($query3)==0){
        echo "EMPTY";
        echo "\n\t\tDOWNLOADING ALL...";
        $this->download_all($shared_db,$local_db,$my_fed);
      }else if($shared_r2["id"] > $local_r2["shared_id"]){
        $this->download_after_offset($local_r2["shared_id"],$shared_db,$local_db,$my_fed);
      }


      echo "\n############### SLEEP $sleep_time... ################";
      sleep($sleep_time);


    }

    $local_db->close();
    $shared_db->close();
    echo "\nLocal and Shared db connections closed";
  }

  //uploads data from left database (starting from row $offset_left) to right database
  private function upload_after_offset($offset_left,$db_left,$db_right,$my_fed){
      $str="select * from test_table where id > $offset_left;";
      $result=$db_left->query($str);
      while($row=mysqli_fetch_array($result)){
        $str="insert into test_table values (null,".$row["time"].",".$row["id"].",'$my_fed')";
        $db_right->query($str);
        echo "\n\t>> id ".$row["id"]." uploaded";
      }
  }

  //uploads data from left database to right database
  private function upload_all($db_left,$db_right,$my_fed){
    $query_tmp=$db_left->query("select * from test_table where id > 0;");
    while($row=mysqli_fetch_array($query_tmp)){
      $str="insert into test_table values(null,".$row["time"].",".$row["id"].",'$my_fed')";
      $db_right->query($str);
    }
  }


  private function download_after_offset($offset,$db_left,$db_right,$my_fed){
    $query=$db_left->query("select * from test_table where id_fd not like '$my_fed' AND id > $offset");
    while($row=mysqli_fetch_array($query)){
      echo "\n\t\t\t>> ROW ID: ".$row["id"];
      $string="insert into test_table values(null,".$row["time"].",'".$row["id_fd"]."',".$row["remote_id"].",".$row["id"].");";
      $db_right->query($string);
    }
  }

  private function download_all($db_left,$db_right,$my_fed){
    $query=$db_left->query("select * from test_table where id_fd not like '$my_fed';");
    while($row=mysqli_fetch_array($query)){
      echo "\n\t\t\t>> ROW ID: ".$row["id"];
      $string="insert into test_table values(null,".$row["time"].",'".$row["id_fd"]."',".$row["remote_id"].",".$row["id"].");";
      echo "\n\t\t\t$string";
      $db_right->query($string);
    }
  }
}
