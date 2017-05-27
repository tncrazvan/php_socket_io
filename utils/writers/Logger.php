<?php
class Logger{
    private $filename,$handler;
    public function Logger($filename){
      $this->filename = $filename;
    }

    public function put($msg){
      echo $msg;
      file_put_contents($this->filename, $msg, FILE_APPEND);
    }
}
