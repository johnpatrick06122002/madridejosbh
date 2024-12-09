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
$tableName = "admins";

// Drop reset_token column if it exists
if (columnExists($dbconnection, $tableName, 'reset_token')) {
    $dropQuery = "ALTER TABLE `$tableName` DROP COLUMN `reset_token`";
    if (mysqli_query($dbconnection, $dropQuery)) {
        echo "Column 'reset_token' dropped successfully.<br>";
    } else {
        echo "ERROR: Could not drop 'reset_token'. " . mysqli_error($dbconnection) . "<br>";
    }
} else {
    echo "Column 'reset_token' does not exist.<br>";
}

// Rename reset_token_expiry to otp_expiry
if (columnExists($dbconnection, $tableName, 'reset_token_expiry')) {
    $renameQuery = "ALTER TABLE `$tableName` CHANGE `reset_token_expiry` `otp_expiry` DATETIME";
    if (mysqli_query($dbconnection, $renameQuery)) {
        echo "Column 'reset_token_expiry' renamed to 'otp_expiry' successfully.<br>";
    } else {
        echo "ERROR: Could not rename 'reset_token_expiry' to 'otp_expiry'. " . mysqli_error($dbconnection) . "<br>";
    }
} else {
    echo "Column 'reset_token_expiry' does not exist.<br>";
}

// Add verification_token column if it doesn't exist
if (!columnExists($dbconnection, $tableName, 'verification_token')) {
    $addQuery = "ALTER TABLE `$tableName` ADD `verification_token` VARCHAR(64) DEFAULT NULL";
    if (mysqli_query($dbconnection, $addQuery)) {
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
