<?php

class TextMessage64{

  private $content64;
  public function __construct($content){
    $this->content64=$content;
  }

  public function send_to($socket){

    $data
    //encoding everything in base64
    =base64_encode(
      //encoding the array in json
      json_encode(
        array(
          "content-type"=>"text-plain",
          "content64"=>$this->content64
        )
      )
    );
    socket_write($socket,$data,strlen($data));
  }

}
