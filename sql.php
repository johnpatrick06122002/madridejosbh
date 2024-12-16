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
// Query to fetch all rows from the 'rental' table
$sql = "SELECT * FROM paid";

$result = mysqli_query($dbconnection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($dbconnection));
}

// Fetch column names dynamically
$fields = mysqli_fetch_fields($result);

echo "<h2>Rental Table Data</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<thead><tr>";

// Display column headers
foreach ($fields as $field) {
    echo "<th>" . htmlspecialchars($field->name) . "</th>";
}

echo "</tr></thead><tbody>";

// Display table data
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . htmlspecialchars($value) . "</td>";
    }
    echo "</tr>";
}

echo "</tbody></table>";

// Free result set and close the connection
mysqli_free_result($result);
mysqli_close($dbconnection);
?>