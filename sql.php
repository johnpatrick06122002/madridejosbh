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

// SQL query to create the 'payment' table
$sql = "
CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `rental_id` varchar(100) NOT NULL,
  `gcash_reference` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `gcash_picture` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_date_pay` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

// Execute the query
if (mysqli_query($dbconnection, $sql)) {
    echo "Table 'payment' created successfully.";
} else {
    echo "ERROR: Could not create table. " . mysqli_error($dbconnection);
}

// Close the connection
mysqli_close($dbconnection);
?>
