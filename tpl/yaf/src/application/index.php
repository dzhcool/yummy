<?php
if(!extension_loaded("yaf")) die('Not Install Yaf');

define('APP_PATH', realpath(dirname(__FILE__)));

require_once(APP_PATH."/library/startup.php");

$yaf = new YafEngine();
$yaf->run();
