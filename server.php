<?php

if(!($socket=socket_create(AF_INET, SOCK_STREAM,0))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);

	die("Couldn't create socket: [$errorcode] $errormsg");
}

echo "Socket created\n";

//Bind the Source address

if(!socket_bind($socket,"127.0.0.1",5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("Could not bind socket: [$errorcode] $errormsg");
}

echo "Socked bind OK\n";


if(!socket_listen($socket,10)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("Socket can't listen:  [$errorcode] $errormsg");
}
echo "Socket is now listening...\n";

$client=socket_accept($socket);

if(socket_getpeername($client,$address,$port)){
	echo "Client $address:$port is now connected to us.\n";
}

socket_close($client);
socket_close($socket);







