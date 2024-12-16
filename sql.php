<?php
// Check if constants are already defined before defining them
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

// Check connection
if ($dbconnection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Query to alter the 'otp' table to make otp_id auto-increment
$alterTableSql = "ALTER TABLE otp MODIFY COLUMN otp_id INT(11) NOT NULL AUTO_INCREMENT;";

if (mysqli_query($dbconnection, $alterTableSql)) {
    echo "The otp_id column has been successfully set to AUTO_INCREMENT.";
} else {
    echo "ERROR: Could not alter the otp table. " . mysqli_error($dbconnection);
}

// Close the connection
mysqli_close($dbconnection);
?>
