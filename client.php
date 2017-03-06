<?php
require_once("./utils/TextMessage64.php");
require_once("./utils/File64.php");

function end_conversation($socket){
	//"|" è il carattere che rappresenta la fine della comunicazione
	socket_write($socket,"|",strlen("|"));
	//chiudo la connessione con il server
	socket_close($socket);
}


//creazione socket
if(!($socket=socket_create(AF_INET, SOCK_STREAM,0))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket couldn't be created: [$errocode] $errormsg");
}

echo "\nSocket created";

//connessione al server 127.0.0.1:50000
//da modificare per estrare un indirizzo ip
//valido attraverso un domain name invece di
//usare un hard-typing dell'indirizzo singolo
if(!socket_connect($socket,"127.0.0.1",5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket can't connect: [$errorcode] $errormsg");
}

echo "\nConnection enstablished";

//invio diversi messaggi al server
/*$txt=new TextMessage64("Mi chiamo Razvan.");
$txt->send_to($socket);
end_conversation($socket);*/

$file=new File64('./test-file.jpg');
$file->send_to($socket);

end_conversation($socket);
