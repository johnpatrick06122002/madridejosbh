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

// SQL query to create the 'booking' table
$sql = "
CREATE TABLE IF NOT EXISTS `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `book_ref_no` varchar(100) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(225) NOT NULL,
  `contact_number` varchar(80) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'Pending',
  `date_posted` datetime DEFAULT current_timestamp(),
  `confirm_date` datetime DEFAULT NULL,
  `otp_id` int(11) DEFAULT NULL,
  `ratings` float NOT NULL,
  `feedback` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

// Execute the query
if (mysqli_query($dbconnection, $sql)) {
    echo "Table 'booking' created successfully.";
} else {
    echo "ERROR: Could not create table. " . mysqli_error($dbconnection);
}

// Close the connection
mysqli_close($dbconnection);
?>
