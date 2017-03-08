<?php

class DBConnection{
  private $mysqli,
          $user,
          $password,
          $db,
          $query_string_history,
          $query_index,
          $last_error,
          $current_index;
  public function __construct($host,$user,$password,$db){
    $this->mysqli=new mysqli($host,$user,$password,$db);
    $this->host=$host;
    $this->user=$user;
    $this->password=$password;
    $this->db=$db;
    /***
      Inizializzazione variabili utili
    ***/
    $this->query_history=array();
    $this->query_index=-1;
    $this->current_index=-1;
    $this->last_error;
  }

  public function query($string){
    $this->query_index++;
    $this->query_string_history[]=$string;
    return $this->mysqli->query($string);
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
    $this->mysqli->query($this->query_string_history[$query_index]);
  }
}
