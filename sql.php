<?php
// Database connection constants
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

// Connect to the database
$dbconnection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($dbconnection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// SQL to drop unique constraint on the `lastname` column
$sql = "ALTER TABLE booking DROP INDEX lastname";

if (mysqli_query($dbconnection, $sql)) {
    echo "Unique constraint removed from 'lastname' column successfully.";
} else {
    echo "ERROR: Could not execute $sql. " . mysqli_error($dbconnection);
}

// Close the database connection
mysqli_close($dbconnection);
?>
