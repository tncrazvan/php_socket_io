<?php
class Message extends Thread{
  private $msg,
          $socket,
          $base64;
  public function Message($socket,$str,$base64=true){
    $this->socket=$socket;
    $this->msg=$str;
    $this->base64=$base64;
  }
  public function run(){
    if($this->base64){
      $base64_converted_msg=base64_encode($this->msg);
      socket_write($this->socket,$base64_converted_msg,strlen($base64_converted_msg));
    }else{
      socket_write($this->socket,$this->msg,strlen($this->msg));
    }

  }
}
