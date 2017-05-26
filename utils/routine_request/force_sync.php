<?php
define("WORKSPACE",__DIR__."/../../");
require_once(WORKSPACE."/utils/writers/RoutineWriter64.php");



(new RoutineWriter64("upload","127.0.0.1",5000,$argv[1]))->start();
(new RoutineWriter64("download","127.0.0.1",5000,$argv[1]))->start();
(new RoutineWriter64("update","127.0.0.1",5000,$argv[1]))->start();
