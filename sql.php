<?php
// Database connection constants
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

// Connect to the database
$dbconnection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($dbconnection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// SQL to describe the `booking` table
$sql = "DESCRIBE booking";

if ($result = mysqli_query($dbconnection, $sql)) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>Field</th>
            <th>Type</th>
            <th>Null</th>
            <th>Key</th>
            <th>Default</th>
            <th>Extra</th>
          </tr>";

    // Fetch and display each row of the table structure
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Free result set
    mysqli_free_result($result);
} else {
    echo "ERROR: Could not execute $sql. " . mysqli_error($dbconnection);
}

// Close the database connection
mysqli_close($dbconnection);
?>
