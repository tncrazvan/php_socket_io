<?php
class SyncTest extends Thread{
  public function run(){
    $db=new DBConnection("151.40.241.114","raz","raz","test",3306);
    $query=$db->query("select * from test_table order by id desc limit 5");
    $tmp="";
    while($riga=mysqli_fetch_array($query)){
      $tmp.=$riga["id"];
    }

    echo "\n".sha1($tmp);
    $t=time();
    echo "\n----------------------\nCONNESSIONE: $t\n--------------------------\n";
    $db->close();
  }
}
