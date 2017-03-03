<?php
require_once("./utils/ServerReader.php");
$allowed_to_run=true;

if(!($socket=socket_create(AF_INET, SOCK_STREAM,0))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);

	die("\nCouldn't create socket: [$errorcode] $errormsg");
}

echo "\nSocket created";

//Bind the Source address

if(!socket_bind($socket,"127.0.0.1",5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nCould not bind socket: [$errorcode] $errormsg");
}

echo "\nSocked bind OK";


if(!socket_listen($socket,10)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket can't listen:  [$errorcode] $errormsg");
}
echo "\nSocket is now listening...";

while($allowed_to_run){
	$client=socket_accept($socket);
	$sr=new ServerReader($client,1);
	$sr->start();
}

socket_close($client);
socket_close($socket);
