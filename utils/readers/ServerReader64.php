<?php
require_once("./utils/readers/Reader64.php");
require_once("./utils/database/DBConnection.php");
require_once("./utils/database/SyncTest.php");
class ServerReader64 extends Reader64{
  function ServerReader($sender_socket,$bytes=1){
    //istanzio l'oggetto attuale e chiamo il costruttore del parent astratto "Reader"
    parent::Reader64($sender_socket,$bytes);
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
        //e il server continuerà ad ascoltare il canale di comunicazione a vuoto.

  */

  protected function callback($result,$address,$port){
    //callback code

    echo "\n$result";

    /*
    $data=json_decode($result,true);

    print("\nTipo del contenuto: ".$data["content-type"]);
    switch($data["content-type"]){
      case "image-jpg":
        print("\nFile-short-name: ".$data["file-short-name"]);
        print("\nFile-extension: ".$data["file-extension"]);
        echo "\n";
        file_put_contents($data["file-short-name"]."-copy.".$data["file-extension"], base64_decode($data["content64"]));
      break;

      case "text-plain":
          print("\nRESULT: ".$data["content64"]);
          echo "\n";
          $test=new SyncTest();
          $test->start();
      break;
      case "insert-notice":

        $db = new DBConnection("127.0.0.1", "root", "root" , "test");
        $time=time();
        $db->query("insert into test_table values(null,$time);");
      break;
    }*/


  }
}
