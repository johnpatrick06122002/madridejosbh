<?php

define('DB_SERVER', ' 127.0.0.1');
define('DB_USERNAME', 'u510162695_bhouse_root');
define('DB_PASSWORD', '1Bhouse_root');
define('DB_NAME', 'u510162695_bhouse');
$dbconnection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($dbconnection === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

?>