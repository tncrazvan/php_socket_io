<?php

class FileMessage64{

  private $content64,
          $name,
          $extension,
          $short_name;

  public function __construct($name){
    $this->name=$name;
    //encoding the file's contents in base64
    $this->content64=base64_encode(file_get_contents($name));
    //separate string on "/"
    $e=explode("/",$name);

    //select the last item of the resulting array
    $e=$e[count($e)-1];


    /*
      Note: by "short name" I mean the name of the file relative to it's absolute location.
        e.g:
          given the file /var/www/html/index.html,
          it's name, short name, and extension are:

          file name: "/var/www/html/index.html"
          short name: "index"
          extension: "html"
    */

    //extracting the short name of the file
    preg_match("/.*(?=\.)/",$e,$this->short_name);
    $this->short_name=$this->short_name[0];

    //extracting the extension of the file
    preg_match("/(?<=\.)[A-z0-9]*$/",$e,$this->extension);
    $this->extension=$this->extension[0];
  }

  public function send_to($socket){
    $data
    //encoding everything in base64
    =base64_encode(
      //encoding the array in json
      json_encode(
        array(
          "content-type"=>"image-jpg",
          "file-size"=>filesize($this->name),
          "file-name"=>$this->name,
          "file-short-name"=>$this->short_name,
          "file-extension"=>$this->extension,
          /*
            IMPORTANT:
              content64 is already encoded in base64 for a specific reason:
                not encoding the file's contents in base64 will result into
                json_decode() failing to decode the json string properly
                on the other side of the socket.
                That is because the file's content might contains weird/wild characters,
                which will escape the json_encode() function, and will result into either
                prematurely ending the decode of the string, or will just throw an error.

                IN CONCLUSION: the content of the file must be encoded in base64
                before the actual json string is encoded in base64.
          */
          "content64"=>$this->content64
        )
      )
    );
    socket_write($socket,$data,strlen($data));

  }

}
