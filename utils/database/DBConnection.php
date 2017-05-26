<?php

class DBConnection {
  private $mysqli,
          $user,
          $password,
          $db,
          $port,
          $query_string_history,
          $query_index,
          $last_error,
          $current_index,
          $autocommit=false;
  public function __construct($host,$settings,$password=null,$db=null,$port=null){
    /*
      if login is not provided, check if $settings is an array,
      if it is an array, then assume that array contains the login details
      and use it to login the database
    */
    if(is_null($password) && is_null($db) && is_null($port) && is_array($settings)){
      switch($host){
        case "local":
        case "127.0.0.1":
        case "localhost":
          $this->mysqli=mysqli_connect(
            $settings["local_address"],
            $settings["local_username"],
            $settings["local_password"],
            $settings["local_database_name"],
            $settings["local_database_port"]);

          $this->user = $settings["local_username"];
          $this->password = $settings["local_password"];
          $this->db = $settings["local_database_name"];
        break;
        case "shared":
        case "sharedhost":
          $this->mysqli=mysqli_connect(
            $settings["shared_address"],
            $settings["shared_username"],
            $settings["shared_password"],
            $settings["shared_database_name"],
            $settings["shared_database_port"]);

          $this->user = $settings["shared_username"];
          $this->password = $settings["shared_password"];
          $this->db = $settings["shared_database_name"];
        break;
      }
    }else{
      //treat $settings as if it was the username of the login
      $this->user = $settings;
      $this->password = $password;
      $this->db = $db;
    }

    $this->query_history=array();
    $this->query_index=-1;
    $this->current_index=-1;
    $this->last_error;
  }

  public function commit(){
    return $this->mysqli->commit();
  }

  public function autocommit($value){
    $this->autocommit=$value;
    return $this->mysqli->autocommit($value);
  }

  public function prepare($string){
    return $this->mysqli->prepare($string);
  }

  public function query($string){
    $this->query_index++;
    $this->query_string_history[]=$string;
    return $this->mysqli->query($string);
  }

  public function get_connect_error(){
    return $this->mysqli->connect_error;
  }
  public function get_error(){
    return $this->mysqli->error;
  }

  public function select_query_index($index){
    if($index >= 0) {
        $current_index=$index;
        return true;
    }
    return false;
  }

  public function select_query_next(){
    if($this->query_index+1<$this->query_string_history)
      $this->current_index++;
  }

  public function select_query_prev(){
    if($this->query_index-1 >= 0)
      $this->current_index--;
  }

  public function execute_selected_query(){
    $tmp = $this->mysqli->query($this->query_string_history[$query_index]);
    if(!$this->autocommit) $tmp->commit();
  }

  public function close(){
    $this->mysqli->close();
  }
}
