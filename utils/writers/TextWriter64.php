<?php
require_once("./utils/writers/Writer64.php");
require_once("./utils/messages/TextMessage64.php");
class TextWriter64 extends Writer64{
  private $message;
  public function __construct($message,$ipv4_address,$port){
    parent::__construct($ipv4_address,$port);
    $this->message=$message;
  }

  protected function callback($socket){
    $tmp=new TextMessage64($this->message);
    $tmp->send_to($socket);
  }
}
