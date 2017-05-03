<?php
require_once("./utils/writers/RoutineWriter64.php");



(new RoutineWriter64("upload",0,"127.0.0.1",5000))->start();
