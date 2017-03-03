<?php
require_once("./utils/Message.php");

$allowed_to_send=true;
if(!($socket=socket_create(AF_INET,SOCK_STREAM,0))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket couldn't be created: [$errocode] $errormsg");
}

echo "\nSocket created";

if(!socket_connect($socket,"127.0.0.1",5000)){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nSocket can't connect: [$errorcode] $errormsg");
}

echo "\nConnection enstablished";



$message=new Message($socket,"Hello world!");
$message->start();
$message=new Message($socket,"How are you?");
$message->start();
$message=new Message($socket,"|",false);
$message->start();

//Now receive reply from server
/*if(socket_recv ($socket , $buf , 2045 , MSG_WAITALL ) === FALSE)
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("\nCould not receive data: [$errorcode] $errormsg");
}*/
socket_close($socket);
//print the received message
//echo $buf;
