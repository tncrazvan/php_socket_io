<?php
require_once("./utils/Reader.php");
class ServerReader extends Reader{
  function ServerReader($socket,$bytes=1){
    //istanzio l'oggetto attuale e chiamo il costruttore del parent astratto "Reader"
    parent::Reader($socket,$bytes);
  }


  //funzione di richiamo
  //@override
  //Questa funzione viene chiamata ogni volta che una comunicazione termina
  /*
  ESEMPIO:
    CLIENT_1 :: prepara messaggio "ciao mondo!"
    CLIENT_1 >> invia il messaggio preparato [CODIFICATO IN BASE64? = YES]

    CLIENT_1 :: prepara messaggio "come va?"
    CLIENT_1 >> invia il messaggio preparato [CODIFICATO IN BASE64? = YES]

    CLIENT_1 :: prepara messaggio "|"
    CLIENT_1 >> invia il messaggio preparato [CODIFICATO IN BASE64? = NO]
        //IMPORTANTE:
        //il carattere "|" indica la fine della comunicazione, perciò
        //esso NON DEVE essere mai condificato in base64, perché la sua rappresentazione
        //in caratteri alfabetici potrebbe essere alterata a fine trasmissione
        //e il server continuerà ad ascoltare il canale di comunicazione.

  */

  protected function callback($result,$address,$port){
    //callback code
    echo "\nRESULT FROM $address:$port -> ".base64_decode(substr($result, 0 , strlen($result)-2));
  }
}
