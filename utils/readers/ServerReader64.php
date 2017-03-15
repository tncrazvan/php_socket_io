<?php
require_once("./utils/readers/Reader64.php");
require_once("./utils/database/DBConnection.php");
require_once("./utils/database/Sync.php");
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

    print("\nContent type: ".$data["content-type"]);
    switch($data["content-type"]){
      case "image-jpg":
        print("\nFile-short-name: ".$data["file-short-name"]);
        print("\nFile-extension: ".$data["file-extension"]);
        echo "\n";
        file_put_contents($data["file-short-name"]."-copy.".$data["file-extension"], base64_decode($data["content64"]));
      break;

      case "text-plain":
      
      break;
      case "insert-notice":

        $db = new DBConnection("127.0.0.1", "root", "root" , "test");
        $time=time();
        $db->query("insert into test_table values(null,$time);");
      break;
    }


  }
}
