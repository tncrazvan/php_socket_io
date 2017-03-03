<?php
require_once("./utils/Message.php");

//creazione socket
if(!($socket=socket_create(AF_INET, SOCK_STREAM,0))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket couldn't be created: [$errocode] $errormsg");
}

echo "\nSocket created";

//connession al server 127.0.0.1:50000
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
$message=new Message($socket,"Hello world!");
$message->start();
$message=new Message($socket,"How are you?");
$message->start();
//"|" è il carattere che rappresenta la fine della comunicazione
$message=new Message($socket,"|",false);
$message->start();

//chiudo la connessione con il server
socket_close($socket);
