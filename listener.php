<?php
ignore_user_abort(1);
//let this script run forever
set_time_limit(0);
//give this script infinite ammount of memory
ini_set("memory_imit",-1);

define("WORKSPACE",__DIR__."/");
define("LOG_DIR",time());
define("LOG_FILENAME_TRUNCATE",5); //every 2.7 hours
if(!file_exists(WORKSPACE."utils/logs")){
	mkdir(WORKSPACE."utils/logs" ,0777);
}
if(!file_exists(WORKSPACE."utils/logs/".LOG_DIR."_log")){
	mkdir(WORKSPACE."utils/logs/".LOG_DIR."_log" ,0777);
}
require_once(WORKSPACE."./utils/readers/ServerReader64.php");
require_once(WORKSPACE."./utils/writers/Logger.php");

//parse settings file
$general_ini=parse_ini_file(WORKSPACE."./settings/general.ini");

/*
	$allowed_to_run is a flag which allows the script to loop and accept client connection_status.
	assigning "false" makes it so the server stops listening for connections.
	IMPORTANT: already enstablished connections will not be lost.
*/
$allowed_to_run=true;
if(!($socket=socket_create(AF_INET, SOCK_STREAM,SOL_TCP))){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);

	die("\nCouldn't create socket: [$errorcode] $errormsg");
}

Logger::put("\nSocket created");

/*
	Bind of Source Address.
	This server prepeares its socket for incoming connections.
*/

if(!socket_bind($socket,"127.0.0.1",$general_ini["listener_port"])){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	die("\nCould not bind socket: [$errorcode] $errormsg");
}

Logger::put("\nSocked bind OK");

/*
	Sarting to listen for connections (not blocking call)
*/
if(!socket_listen($socket,$general_ini["listener_port"])){
	$errorcode=socket_last_error();
	$errormsg=socket_strerror($errorcode);
	Logger::put("\nSocket can't listen:  [$errorcode] $errormsg");
}

while($allowed_to_run){
	//accepting 1 incoming connection
	$client=socket_accept($socket);


	$general_ini=parse_ini_file(WORKSPACE."./settings/general.ini");
	/*
		ServerReader64 creates a Reader64, which in turn creates a Thread
		which will read the client's message in chunks of 2048 bytes
	*/
$sr=new ServerReader64($client/*client's socket*/,$general_ini["server_reader_mtu"]/*MTU*/);

	//starting the reader (which is a thread)
	$sr->start();
}


//closing my(server) socket
socket_shutdown($socket);
socket_close($socket);
