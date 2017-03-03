<?php
require_once("./utils/Reader.php");
class ServerReader extends Reader{
  function ServerReader($socket,$bytes=1){
    parent::Reader($socket,$bytes);
  }

  //@override
  protected function callback($result,$address,$port){
    //callback code
    echo "\nRESULT FROM $address:$port -> ".base64_decode(substr($result, 0 , strlen($result)-2));
  }
}
