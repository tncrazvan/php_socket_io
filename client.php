<?php
require_once("./utils/messages/TextMessage64.php");
require_once("./utils/messages/File64.php");


//creazione socket
if(!($socket=socket_create(AF_INET, SOCK_STREAM,SOL_TCP))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket couldn't be created: [$errocode] $errormsg");
}

echo "\nSocket created";

//connessione al server 127.0.0.1:50000
//da modificare per estrare un indirizzo ip
//valido attraverso un domain name invece di
//usare un hard-typing dell'indirizzo singolo
if(!socket_connect($socket,"151.40.241.114",5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket can't connect: [$errorcode] $errormsg");
}

echo "\nConnection enstablished";

//invio diversi messaggi al server

$txt = new TextMessage64("Hello world!");
$txt->send_to($socket);

socket_close($socket);
