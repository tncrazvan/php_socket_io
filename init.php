<?php

define("WORKSPACE",__DIR__);
$general_ini=array();
echo "\nSetting up names...";
$general_ini["location"] = WORKSPACE;
$general_ini["federation_name"]="unknown";
$general_ini["local_address"]="127.0.0.1";
$general_ini["local_username"]="unknown";
$general_ini["local_password"]="unknown";
$general_ini["local_database_name"]="unknown";
$general_ini["local_database_port"]=3306;
$general_ini["upload_limit"]=100;
$general_ini["download_limit"]=100;
$general_ini["update_limit"]=100;

$general_ini["shared_address"]="unknown";
$general_ini["shared_username"]="unknown";
$general_ini["shared_password"]="unknown";
$general_ini["shared_database_name"]="glorep_shared";
$general_ini["shared_database_port"]=3306;

$general_ini["listener_port"]=5000;
$general_ini["server_reader_mtu"]=2048;

$general_ini["sleep_time"]=10;

echo "\nOpening file......";
$handler = fopen(WORKSPACE."./settings/general.ini", "w");
$tmp = "";
echo "\nWritting:\n";
foreach($general_ini as $key => $value){
  switch ($key) {
    case 'location':
      $tmp .=";################ Location of GSIO #########################\n";
      echo ";################ Location of GSIO #########################\n";
      break;
    case 'federation_name':
      $tmp .="\n\n\n\n;################ LOCAL SERVER DETAILS #####################\n";
      echo "\n\n\n\n;################ LOCAL SERVER DETAILS #####################\n";
      break;
    case 'shared_address':
      $tmp .="\n\n\n\n;################# SHARED SERVER DETAILS ###################\n";
      echo "\n\n\n\n;################# SHARED SERVER DETAILS ###################\n";
      break;
    case 'listener_port':
      $tmp .="\n\n\n\n;################# CLIENT LISTENER DETAILS #################\n";
      echo "\n\n\n\n;################# CLIENT LISTENER DETAILS #################\n";
      break;
    case 'sleep_time':
      $tmp .="\n\n\n\n;################# GENERIC DETAILS #########################\n";
      $tmp .=";sleep time of the routine (in seconds)\n";
      echo "\n\n\n\n;################# GENERIC DETAILS #########################\n";
      echo ";sleep time of the routine (in seconds)\n";
      break;
  }

  $tmp .="$key = $value\n";
  echo "\n$key = $value\n";

}
fwrite($handler, $tmp);
echo "\nDone.";
fclose($handler);
echo "\nFile closed.";
