<?php
require_once("./utils/writers/TextWriter64.php");
require_once("./utils/writers/FileWriter64.php");

(new TextWriter64("hello","127.0.0.1",5000))->start();
(new FileWriter64("./test-file.jpg","127.0.0.1",5000))->start();
