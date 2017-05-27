<?php
require_once(WORKSPACE."./utils/writers/Logger.php");
abstract class Reader64 extends Thread{
  private
          //socket of the receiver
          $sender_socket=null,
          //sender's ip address
          $address=null,
          //sender's port
          $port=null,
          //number of bytes to read at once
          $mtu,
          //result of the final message (the whole message, not just a chunk of N bytes)
          $result="";
  public function Reader64($sender_socket,$mtu){

    //saving sender's socket
    $this->sender_socket=$sender_socket;
    //saving number of bytes to read each iteration
    $this->mtu=$mtu;
    //create a logger using the same file the routine is using at this moment
  }

  public function run(){

    //getting parameters from sender
    if(socket_getpeername($this->sender_socket,$address,$port)){
    	$this->address=$address;
      $this->port=$port;
    }
    Logger::put("\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
    Logger::put("\n\t-> Connected to client $this->address:$this->port");
    $this->result="";

    $i=0;
    //read once, then keep reading if the sender is still sending data
    do{
        $i++;
        //reading 2048 bytes at once (check server.php line 55)
        $line=@socket_read($this->sender_socket,$this->mtu);//MTU=2048
        //if the chunk that I'm reading contains anything
        if($line){
          //append it to the final result
          $this->result.=$line;
        }
    }while($line);
    //calback method.This is just a quality of life choice,
    //the body of this abstract method is defined in ./utils/readers/ServerReader64.php
    $this->callback(base64_decode($this->result),$this->address,$this->port);
    socket_shutdown($this->sender_socket);
    Logger::put("\n\t<- Connection ended");
    Logger::put("\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
  }

  //callback method
  abstract protected function callback($result,$address,$port);
}
