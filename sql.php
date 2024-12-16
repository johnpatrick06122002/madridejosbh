<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!defined('DB_SERVER')) {
    define('DB_SERVER', '127.0.0.1');
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'u510162695_bhouse_root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '1Bhouse_root');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'u510162695_bhouse');
}

// Establish database connection
$dbconnection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$dbconnection) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
} else {
    echo "Connection successful!";
}
?>
