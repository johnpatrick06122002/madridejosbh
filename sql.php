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

// Fetch all data from register1 table
$query = "SELECT * FROM register2";
$result = mysqli_query($dbconnection, $query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr>";

    // Fetch and display column headers
    $fields = mysqli_fetch_fields($result);
    foreach ($fields as $field) {
        echo "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    echo "</tr>";

    // Fetch and display each row of data
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $column) {
            echo "<td>" . htmlspecialchars($column) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "ERROR: Could not execute query: $query. " . mysqli_error($dbconnection);
}

// Close the database connection
mysqli_close($dbconnection);
?>
