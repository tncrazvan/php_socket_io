<?php
require_once("./utils/writers/Writer64.php");
require_once("./utils/messages/File64.php");
class FileWriter64 extends Writer64{
  private $file_name;
  public function __construct($file_name,$ipv4_address,$port){
    parent::__construct($ipv4_address,$port);
    $this->file_name=$file_name;
  }

  protected function callback($socket){
    $tmp=new File64($this->file_name);
    $tmp->send_to($socket);
  }
}
