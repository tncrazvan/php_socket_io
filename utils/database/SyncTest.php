<?php
class SyncTest extends Thread{
  public function run(){
    $local_time_old=0;
    $mia_fed='unipg';
    $shared_db=new DBConnection("127.0.0.1","root","root","shared_test",3306);
    $local_db=new DBConnection("127.0.0.1","root","root","test",3306);


    $query=$local_db->query("select * from test.test_table order by id desc limit 1");
    $query2=$shared_db->query("select * from shared_test.test_table where id_fd like '$mia_fed' order by id desc limit 1");



    $local_r1=mysqli_fetch_array($query);
    /*
    [
      "id"=>13,
      "time"=>1489142998

    ]
    */
    $local_time=$local_r1["time"];
    $local_old_time=$local_time;





    $shared_r1=mysqli_fetch_array($query2);
    $shared_time=$shared_r1["time"];


    echo "\nLOCAL: $local_time (".(mysqli_num_rows($query)).")";
    echo "\nSHARED: $shared_time (".(mysqli_num_rows($query2)).")";


    if(mysqli_num_rows($query2)==0){
      $query_tmp=$local_db->query("select * from test_table where time > 0;");
      while($riga=mysqli_fetch_array($query_tmp)){
        echo "\n##### POPOLAMENT COMPLETO #####";
        $str="insert into test_table values(null,".$riga["time"].",".$riga["id"].",'$mia_fed')";
        $shared_db->query($str);
        echo "\n";
        echo "$str";
        echo "\n----------------";
      }
      $local_old_time=$local_time;
    }else{
      if($local_time > $shared_time){
        echo "\n###### LOCAL IS AHEAD ######";
        $query_tmp=$local_db->query("select * from test_table where time > $shared_time;");
        while($riga=mysqli_fetch_array($query_tmp)){
          $str="insert into test_table values(null,".$riga["time"].",".$riga["id"].",'$mia_fed')";
          $shared_db->query($str);
          echo "\n";
          echo "$str";
          echo "\n----------------";
        }
        $local_old_time=$local_time;
      }else{
        echo "\n##### LOCAL IS NOT BEHIND #####";
      }
    }



    $local_db->close();
    $shared_db->close();
  }
}
