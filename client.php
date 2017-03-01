<?php

if(!($socket=socket_create(AF_INET,SOCK_STREAM,0))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("Socket couldn't be created: [$errocode] $errormsg");
}

echo "Socket created\n";

if(!socket_connect($socket,"127.0.0.1",5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("Socket can't connect: [$errorcode] $errormsg");
}

echo "Connection enstablished\n";

