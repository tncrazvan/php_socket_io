<?php
class SyncTest extends Thread{

  public function run(){
    $general_ini=parse_ini_file("./settings/general.ini");
    $my_fed=$general_ini["federation_name"];

    $shared_db=new DBConnection("127.0.0.1","root","root","shared_test",3306);
    $local_db=new DBConnection("127.0.0.1","root","root","test",3306);


    $query=$local_db->query("select * from test.test_table order by id desc limit 1");
    $query2=$shared_db->query("select * from shared_test.test_table where id_fd like '$my_fed' order by id desc limit 1");
    $local_r1=mysqli_fetch_array($query);
    $shared_r1=mysqli_fetch_array($query2);


    if(mysqli_num_rows($query2)==0){
      $this->upload_all($local_db,$shared_db,$my_fed);
    }else{
      if($local_r1["id"] > $shared_r1["remote_id"]){
        $this->upload_from_offset($shared_r1["remote_id"],$local_db,$shared_db,$my_fed);
      }
    }



    $local_db->close();
    $shared_db->close();
    echo "\nLocal and Shared db connections closed";
  }

  //uploads data from left database (starting from row $offset_left) to right database
  private function upload_from_offset($offset_left,$db_left,$db_right,$my_fed){
      $str="select * from test_table where id > $offset_left;";
      $result=$db_left->query($str);
      while($row=mysqli_fetch_array($result)){
        $str="insert into test_table values (null,".$row["time"].",".$row["id"].",'$my_fed')";
        $db_right->query($str);
        echo "\n id ".$row["id"]." inserted";
      }
  }

  //uploads data from left database to right database
  private function upload_all($db_left,$db_right,$my_fed){
    echo "\n##### POPULATING FROM SKRATCH #####";
    $query_tmp=$db_left->query("select * from test_table where time > 0;");
    while($row=mysqli_fetch_array($query_tmp)){
      $str="insert into test_table values(null,".$row["time"].",".$row["id"].",'$my_fed')";
      $db_right->query($str);

      //debug purposes
      echo "\n";
      echo "$str";
      echo "\n----------------";
    }
  }
}
