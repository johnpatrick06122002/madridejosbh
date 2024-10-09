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

// Establish a database connection
$dbconnection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($dbconnection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Query to fetch all tables from the database
$query = "SHOW TABLES";
$result = mysqli_query($dbconnection, $query);

if ($result) {
    echo "<h1>Tables in the database '" . DB_NAME . "':</h1>";
    echo "<ul>";
    while ($row = mysqli_fetch_row($result)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "ERROR: Could not execute query. " . mysqli_error($dbconnection);
}

// Close the connection
mysqli_close($dbconnection);
?>
