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

// SQL to modify the 'notice' column
$sql = "ALTER TABLE `rental` MODIFY COLUMN `notice` VARCHAR(1000) DEFAULT NULL;";

// Execute the query
if (mysqli_query($dbconnection, $sql)) {
    echo "Column 'notice' updated successfully to VARCHAR(1000).";
} else {
    echo "ERROR: Could not update column 'notice'. " . mysqli_error($dbconnection);
}

// Close the connection
mysqli_close($dbconnection);
?>