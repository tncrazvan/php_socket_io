<?php

class RoutineMessage64{
  private $type,$offset;
  public function __construct($type,$offset){
    $this->type=$type;
    $this->offset=$offset;
  }

  public function send_to($socket){

    $data
    //encoding everything in base64
    =base64_encode(
      //encoding the array in json
      json_encode(
        array(
          "content-type"=>"routine-request",
          "routine-type"=>$this->type,
          "offset"=>$this->offset
        )
      )
    );
    socket_write($socket,$data,strlen($data));
  }

}
