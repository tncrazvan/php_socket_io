<?php
require_once("./utils/writers/Writer64.php");
require_once("./utils/messages/RoutineMessage64.php");
class RoutineWriter64 extends Writer64{
  private $type,$offset;
  public function __construct($type,$offset,$ipv4_address,$port){
    parent::__construct($ipv4_address,$port);
    $this->type=$type;
    $this->offset=$offset;
  }

  protected function callback($socket){
    $tmp=new RoutineMessage64($this->type,$this->offset);
    $tmp->send_to($socket);
  }
}
