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

if ($dbconnection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// SQL query to modify the column to NOT NULL
$sql = "ALTER TABLE booking MODIFY firstname datetime";

// Execute the query
if (mysqli_query($dbconnection, $sql)) {
    echo "The column 'firstname' has been successfully altered to NOT NULL.";
} else {
    echo "ERROR: Could not alter the column. " . mysqli_error($dbconnection);
}

// Close the connection
mysqli_close($dbconnection);
?>
