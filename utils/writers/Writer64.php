<?php

abstract class Writer64 extends Thread{
  private $socket;
  public function __construct($ipv4_address,$port){
    //creating socket
    $this->socket=socket_create(AF_INET, SOCK_STREAM,SOL_TCP);
    //connecting to socket and sending "hello world"
    socket_connect($this->socket,$ipv4_address,$port);
  }

  public function run(){
    $this->callback($this->socket);
    socket_shutdown($this->socket);
  }

  protected abstract function callback($socket);
}
