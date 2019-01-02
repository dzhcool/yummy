<?php
require_once(dirname(__file__)."/../common.php");

for($i = 0; $i < 60; $i++)
{
    echo 'daemon ready!';
    sleep(1);
}
