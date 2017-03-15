<?php
require_once("./utils/readers/ServerReader64.php");
require_once("./utils/database/Sync.php");


$sync = new Sync();
$sync->start();

/*
	$allowed_to_run is a flag which allows the script to loop and accept client connection_status.
	assigning "false" makes it so the server stops listening for connections.
	IMPORTANT: already enstablished connections will not be lost.
*/
$allowed_to_run=true;
//creo un nuovo Thread
if(!($socket=socket_create(AF_INET, SOCK_STREAM,SOL_TCP))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);

	die("\nCouldn't create socket: [$errorcode] $errormsg");
}

echo "\nSocket created";

/*
	Bind of Source Address.
	This server prepeares its socket for incoming connections.
*/

if(!socket_bind($socket,"127.0.0.1",5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nCould not bind socket: [$errorcode] $errormsg");
}

echo "\nSocked bind OK";

/*
	Sarting to listen for connections (not blocking call)
*/
if(!socket_listen($socket,10000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket can't listen:  [$errorcode] $errormsg");
}
echo "\nSocket is now listening...";

while($allowed_to_run){
	echo "\nStarting thread...";
	//accepting 1 incoming connection
	$client=socket_accept($socket);
	//ServerReader crea un Reader64 (utils/Reader64.php)
	//Il quale a sua volta crea un Thread in cui legge i
	//messaggi del client rispettivo, 2048 byte alla volta
	$sr=new ServerReader64($client,2048);

	//eseguo il Thread
	$sr->start();
}
socket_close($socket);
