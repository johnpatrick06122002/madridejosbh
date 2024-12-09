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

if ($dbconnection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Function to check if a column exists in a table
function columnExists($dbconnection, $table, $column) {
    $query = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = mysqli_query($dbconnection, $query);
    return $result && mysqli_num_rows($result) > 0;
}

// Table name
$tableName = "register1";

// Add session_token column if it doesn't exist
if (!columnExists($dbconnection, $tableName, 'session_token')) {
    $alterQuery = "ALTER TABLE `$tableName` ADD `session_token` VARCHAR(255) DEFAULT NULL";
    if (mysqli_query($dbconnection, $alterQuery)) {
        echo "Column 'session_token' added successfully.<br>";
    } else {
        echo "ERROR: Could not add 'session_token'. " . mysqli_error($dbconnection) . "<br>";
    }
} else {
    echo "Column 'session_token' already exists.<br>";
}

// Add verification_token column if it doesn't exist
if (!columnExists($dbconnection, $tableName, 'verification_token')) {
    $alterQuery = "ALTER TABLE `$tableName` ADD `verification_token` VARCHAR(64) DEFAULT NULL";
    if (mysqli_query($dbconnection, $alterQuery)) {
        echo "Column 'verification_token' added successfully.<br>";
    } else {
        echo "ERROR: Could not add 'verification_token'. " . mysqli_error($dbconnection) . "<br>";
    }
} else {
    echo "Column 'verification_token' already exists.<br>";
}

// Close the database connection
mysqli_close($dbconnection);
?>
