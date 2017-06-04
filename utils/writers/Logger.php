<?php
class Logger{
    public static function put($msg,$current_log_dir=LOG_DIR){
      $time=time();
      echo $msg;
      file_put_contents(WORKSPACE."utils/logs/".$current_log_dir.'_log/'.substr($time,0,strlen($current_log_dir)-LOG_FILENAME_TRUNCATE).".log", $msg, FILE_APPEND);
    }
}
