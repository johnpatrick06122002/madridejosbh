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

// Update query to change the email and hashed password for ID 1
$new_email = 'madridejosbh2@gmail.com';
$new_password = 'keneth@12ducay07';

// Hash the password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$sql = "UPDATE admins SET email = ?, password = ? WHERE id = 1";

// Prepare the statement
if ($stmt = mysqli_prepare($dbconnection, $sql)) {
    // Bind parameters
    mysqli_stmt_bind_param($stmt, 'ss', $new_email, $hashed_password);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        echo "Record updated successfully with a hashed password.";
    } else {
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($dbconnection);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    echo "ERROR: Could not prepare query: $sql. " . mysqli_error($dbconnection);
}

// Close the database connection
mysqli_close($dbconnection);
?>
