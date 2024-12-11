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

// Query to describe the table structure
$describe_sql = "DESCRIBE admins";
$describe_result = mysqli_query($dbconnection, $describe_sql);

if ($describe_result) {
    echo "<h3>Table Structure: admins</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

    // Fetch and display table structure
    while ($field = mysqli_fetch_assoc($describe_result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($field['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($field['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($field['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($field['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($field['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($field['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "ERROR: Could not execute $describe_sql. " . mysqli_error($dbconnection);
}

// Query to fetch all data from the table
$data_sql = "SELECT * FROM admins";
$data_result = mysqli_query($dbconnection, $data_sql);

if ($data_result) {
    if (mysqli_num_rows($data_result) > 0) {
        echo "<h3>Data in the admins Table</h3>";
        echo "<table border='1'>";

        // Fetch and display column headers dynamically
        $fields = mysqli_fetch_fields($data_result);
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";

        // Fetch and display data rows
        while ($row = mysqli_fetch_assoc($data_result)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "The admins table is empty.";
    }
} else {
    echo "ERROR: Could not execute $data_sql. " . mysqli_error($dbconnection);
}

// Close the database connection
mysqli_close($dbconnection);
?>
