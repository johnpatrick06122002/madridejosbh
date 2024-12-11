<?php
// Database connection configuration
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

// Establish the database connection
$dbconnection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check the connection
if ($dbconnection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// SQL to modify the otp column to VARCHAR(255)
$sql = "ALTER TABLE admins MODIFY COLUMN otp VARCHAR(255)";

if (mysqli_query($dbconnection, $sql)) {
    echo "Column 'otp' modified to VARCHAR(255) successfully.";
} else {
    echo "ERROR: Could not modify column 'otp'. " . mysqli_error($dbconnection);
}

// Close the database connection
mysqli_close($dbconnection);
?>
