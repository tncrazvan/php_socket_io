<?php
//I messaggi vengono inviati in in Threaded
//cioò significa che è possibile mandare più messaggi contemporaneamente a più server.
//IMPORTANTE: questo significa che i messaggi inviati allo stesso server non sono sincronizzati.
//            Questo è il motivo dell'utilizzo della variabile $busy.
class Message extends Thread{
  //variabile che servirà per esprimere se un messaggio è in corso di invio o meno
  private $busy=false,
          $msg,
          $socket,
          //variabile booleana che mi esprime se la stringa in input dovrà essere convertita in base64 (true) o no (false)
          $base64;
  public function Message($socket,$str,$base64=true){
    //salvo il socket del destinatario
    $this->socket=$socket;
    //salvo il messaggio
    $this->msg=$str;
    //base64 mi permette di capire se dovrò convertire $str in base64 o no
    $this->base64=$base64;
  }

  /*
    are_busy($message) mi permette di capire se una lista di messaggi è in fase di invio (restituisce true) o no (restituisce false)
    @param $message: lista di oggetti Message nella forma di un array(), oppure un singolo oggetto Message.
  */
  public static function are_busy($message){
    if(is_array($message)){
      foreach ($message as $item) {
        if($item->is_busy()){
          return true;
        }
      }
      return false;
    }
    return $message->is_busy();
  }

  //mi restituisce true se il Message è busy, false altrimenti.
  public function is_busy(){
    return $this->busy;
  }

  public function run(){
    //il Message inizia la fase di spedizione, quindi è busy
    $this->busy=true;
    //controllo se devo convertire $this->msg in base64
    if($this->base64){
      //converto in base64
      $base64_converted_msg=base64_encode($this->msg);
      //spedisco il messaggio convertito in base64
      socket_write($this->socket,$base64_converted_msg,strlen($base64_converted_msg));
    }else{
      //prendo il messaggio pulito e lo spedisco
      socket_write($this->socket,$this->msg,strlen($this->msg));
    }
    //spedizione terminata, quindi non è busy
    $this->busy=false;
  }
}
