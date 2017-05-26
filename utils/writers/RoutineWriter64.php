<?php
require_once(WORKSPACE."./utils/writers/Writer64.php");
require_once(WORKSPACE."./utils/messages/RoutineMessage64.php");
class RoutineWriter64 extends Writer64{
  private $type,$limit;
  public function __construct($type,$ipv4_address,$port,$limit=100){
    parent::__construct($ipv4_address,$port);
    $this->type=$type;
    $this->limit=$limit;
  }

  protected function callback($socket){
    $tmp=new RoutineMessage64($this->type,$this->limit);
    $tmp->send_to($socket);
  }
}
