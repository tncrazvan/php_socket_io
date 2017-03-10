<<?php
abstract class Reader64 extends Thread{
  private
          //socket del ricevente
          $socket=null,
          //indirizzo ip del mittente
          $address=null,
          //porta del mittente
          $port=null,
          //numero di byte da leggere
          $bytes,
          //risultato del messaggio finale (tutta la stringa letta a termine comunicazione)
          $result="";
  public function Reader64($socket,$bytes){

    //salvo il socket del ricevente
    $this->socket=$socket;
    //salvo il numero di byte da leggere per ogni pezzo di stringa
    $this->bytes=$bytes;
  }

  public function run(){
    if(socket_getpeername($this->socket,$address,$port)){
    	$this->address=$address;
      $this->port=$port;
    }

    echo "\nThread started";
    $this->result="";

    echo "\n";
    $i=0;
    //leggo una volta e continuo a leggere se il mittente scrive qualsiasi cosa diversa da "|"
    do{
        $i++;
        //leggo 204s byte e lo salvo in $line
        $line=@socket_read($this->socket,2048);//MTU=2048
        //se il byte che ho letto è diverso da "|"...
        if($line){
          echo "[$line]";
          //...lo appendo a $this->result
          $this->result.=$line;
        }
    }while($line);
    
    //funzione di richiamo (controlla utils/ServerReader64.php)
    $this->callback($this->socket,base64_decode($this->result),$this->address,$this->port);

  }

  //funzione di richiamo (controlla utils/ServerReader64.php)
  abstract protected function callback($socket,$result,$address,$port);
}
