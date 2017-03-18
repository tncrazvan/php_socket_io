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
if(!socket_listen($socket,5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket can't listen:  [$errorcode] $errormsg");
}

while($allowed_to_run){
	//accepting 1 incoming connection
	$client=socket_accept($socket);
	/*
		ServerReader64 creates a Reader64, which in turn creates a Thread
		which will read the client's message in chunks of 2048 bytes
	*/
	$sr=new ServerReader64($client/*client's socket*/,2048/*MTU*/);

	//starting the reader (which is a thread)
	$sr->start();
}


//closing my(server) socket
socket_shutdown($socket);
socket_close($socket);
