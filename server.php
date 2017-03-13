<?php
require_once("./utils/readers/ServerReader64.php");

//E' un flag che permette allo script di accettare
//continuamente le richieste di connessione da parte dei client
//assegnando $allowed_to_run=false fa in modo che il server non accetti più
//alcune richiesta.
//IMPORTANTE: $allowed_to_run=false non significa che lo script smette di funzionare,
//						se una connessione è stata stabilità prima che l'assegnazione $allowed_to_run=false
//						sia avvenuta, tale connesione tra server (questo script) e client, viene mantenuta
//						finché non non verrà interrotta specificatamente dal server o dal client.
//						QUINDI: $allowed_to_run=false non ferma connessioni già stabilite.
//						Fermare una specifica connessione è uno lavoro per socket_close($client),
//						dove $client è il socket del client.
$allowed_to_run=true;
//creo un nuovo Thread
if(!($socket=socket_create(AF_INET, SOCK_STREAM,SOL_TCP))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);

	die("\nCouldn't create socket: [$errorcode] $errormsg");
}

echo "\nSocket created";

//Bind del Source address
//ovvero: il server prepara il proprio socket per i client
//NOTA: è diverso da socket_connect()
//			socket_bind viene eseguito dal server
//			mentre socket_connect viene eseguito dal client
if(!socket_bind($socket,"127.0.0.1",5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nCould not bind socket: [$errorcode] $errormsg");
}

echo "\nSocked bind OK";

//Metto il a disposizione il mio socket per i
//client che si vogliono connettere (listening)
if(!socket_listen($socket,10000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket can't listen:  [$errorcode] $errormsg");
}
echo "\nSocket is now listening...";


//finché $allowed_to_run=true...
while($allowed_to_run){
	echo "\nStarting thread...";
	//... accetto le richieste del client
	//NOTA: da modificare per filtrare i client
	//		esempio: accetto solo i client che sono
	//		sotto il dominio di unipg.it
	$client=socket_accept($socket);
	//ServerReader crea un Reader64 (utils/Reader64.php)
	//Il quale a sua volta crea un Thread in cui legge i
	//messaggi del client rispettivo, 2048 byte alla volta
	$sr=new ServerReader64($client,2048);

	//eseguo il Thread
	$sr->start();
}
socket_close($socket);
