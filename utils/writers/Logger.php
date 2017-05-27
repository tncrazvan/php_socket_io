<?php
class Logger{
    public static function put($msg,$filename=LOG_FILE){
      echo $msg;
      file_put_contents($filename, $msg, FILE_APPEND);
    }
}
