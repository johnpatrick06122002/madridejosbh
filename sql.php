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

// Change the 'id' column in the 'book' table to INT(6) and remove AUTO_INCREMENT
$alterQuery = "ALTER TABLE book MODIFY id INT(6) NOT NULL";
if (mysqli_query($dbconnection, $alterQuery)) {
    echo "Column 'id' in the 'book' table modified successfully.<br>";
} else {
    echo "ERROR: Could not modify the 'id' column. " . mysqli_error($dbconnection) . "<br>";
}

// Close the database connection
mysqli_close($dbconnection);
?>
