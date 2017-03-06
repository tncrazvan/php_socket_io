<?php

class TextMessage64{
  private $content64;
  public function __construct($content){
    $this->content64=$content64;
  }

  public function send_to($socket){
    $data
    =base64_encode(
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
