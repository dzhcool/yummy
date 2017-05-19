<?php
define('APP_PATH', realpath(dirname(__FILE__)));

require_once(APP_PATH."/../src/application/library/startup.php"));

$yaf = new YafEngine();
$yaf->run();
