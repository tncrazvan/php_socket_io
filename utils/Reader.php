<<?php
abstract class Reader extends Thread{
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
  public function Reader($socket,$bytes){
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
        //leggo 1 byte e lo salvo in $line
        $line=@socket_read($this->socket,1,PHP_NORMAL_READ);
        //se il byte che ho letto è diverso da "|"...
        if($line != "|"){
          //echo "[$line]";
          //...lo appendo a $this->result
          $this->result.=$line;
        }
    }while($line != "|");
    //funzione di richiamo (controlla utils/ServerReader.php)
    $this->callback(base64_decode($this->result),$this->address,$this->port);

    socket_close($this->socket);
  }

  //funzione di richiamo (controlla utils/ServerReader.php)
  abstract protected function callback($result,$address,$port);
}
