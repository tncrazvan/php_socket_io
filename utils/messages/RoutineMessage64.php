<?php

class RoutineMessage64{
  private $type,$limit;
  public function __construct($type,$limit=100){
    $this->type=$type;
    $this->limit=$limit;
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
          "limit"=>$this->limit
        )
      )
    );
    socket_write($socket,$data,strlen($data));
  }

}
