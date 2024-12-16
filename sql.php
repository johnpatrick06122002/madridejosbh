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

// SQL query to add 'qr_code' and 'notice' columns to the 'rental' table
$sql = "
ALTER TABLE `rental`
ADD COLUMN `qr_code` varchar(1000) DEFAULT NULL,
ADD COLUMN `notice` varchar(225) DEFAULT NULL;
";

// Execute the query
if (mysqli_query($dbconnection, $sql)) {
    echo "Columns 'qr_code' and 'notice' added successfully to the 'rental' table.";
} else {
    echo "ERROR: Could not modify table. " . mysqli_error($dbconnection);
}

// Close the connection
mysqli_close($dbconnection);
?>
