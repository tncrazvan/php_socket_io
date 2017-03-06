<?php

class File64{

  private $content64,
          $name,
          $extension,
          $short_name;

  public function __construct($name){
    $this->name=$name;
    $this->content64=base64_encode(file_get_contents($name));
    //explode su "/"
    $e=explode("/",$name);
    //seleziono l'ultimo elemento della explode
    $e=$e[count($e)-1];


    //estraggo il short name da $e
    preg_match("/.*(?=\.)/",$e,$this->short_name);
    $this->short_name=$this->short_name[0];


    //estraggo l'estensione da $e
    preg_match("/(?<=\.)[A-z0-9]*$/",$e,$this->extension);
    $this->extension=$this->extension[0];
  }

  public function send_to($socket){
    $data
    =base64_encode(
      json_encode(
        array(
          "content-type"=>"image-jpg",
          "file-name"=>$this->name,
          "file-short-name"=>$this->short_name,
          "file-extension"=>$this->extension,
          "content64"=>$this->content64
        )
      )
    );
    socket_write($socket,$data,strlen($data));

  }

}
