<<?php
abstract class Reader extends Thread{
  private $socket=null,
          $address=null,
          $port=null,
          $bytes,
          $result="";
  public function Reader($socket,$bytes){
    $this->socket=$socket;
    $this->bytes=$bytes;
  }

  public function run(){
    if(socket_getpeername($this->socket,$address,$port)){
    	$this->address=$address;
      $this->port=$port;
    }

    echo "\nThread started";
    //read data from the incoming socket
    $this->result="";

    do{
        $line=@socket_read($this->socket,1);
        echo "\n[$line]";
        if($line != "|"){
          $this->result.=$line;
        }
    }while($line != "|");
    echo "\nENCODED RESULT: \n".$this->result;
    echo "\nEnd Thread";
    $this->callback(base64_decode($this->result),$this->address,$this->port);
  }


  abstract protected function callback($result,$address,$port);
}
