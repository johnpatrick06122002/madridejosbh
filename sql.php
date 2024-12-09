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

// Check if a delete action is triggered
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']); // Sanitize the input
    $deleteQuery = "DELETE FROM register1 WHERE id = $deleteId";
    if (mysqli_query($dbconnection, $deleteQuery)) {
        if (mysqli_affected_rows($dbconnection) > 0) {
            echo "Record with ID $deleteId deleted successfully.<br>";
        } else {
            echo "No record found with ID $deleteId.<br>";
        }
    } else {
        echo "ERROR: Could not execute delete query. " . mysqli_error($dbconnection) . "<br>";
    }
}

// Fetch all data from register1 table
$query = "SELECT * FROM register1";
$result = mysqli_query($dbconnection, $query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr>";

    // Fetch and display column headers
    $fields = mysqli_fetch_fields($result);
    foreach ($fields as $field) {
        echo "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    echo "<th>Action</th>"; // Add an action column
    echo "</tr>";

    // Fetch and display each row of data
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $column) {
            echo "<td>" . htmlspecialchars($column) . "</td>";
        }
        // Add Delete link for each row
        echo "<td><a href='?delete_id=" . htmlspecialchars($row['id']) . "' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "ERROR: Could not execute query: $query. " . mysqli_error($dbconnection);
}

// Close the database connection
mysqli_close($dbconnection);
?>
