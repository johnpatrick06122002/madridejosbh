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

// Get the list of all tables in the database
$sql_tables = "SHOW TABLES";
if ($result_tables = mysqli_query($dbconnection, $sql_tables)) {
    echo "<h2>Database: " . DB_NAME . "</h2>";
    while ($row_table = mysqli_fetch_row($result_tables)) {
        $table_name = $row_table[0];
        echo "<h3>Table: $table_name</h3>";
        
        // Get the data from the current table
        $sql_data = "SELECT * FROM `$table_name`";
        if ($result_data = mysqli_query($dbconnection, $sql_data)) {
            if (mysqli_num_rows($result_data) > 0) {
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                
                // Display column headers
                $fields = mysqli_fetch_fields($result_data);
                echo "<tr>";
                foreach ($fields as $field) {
                    echo "<th>" . htmlspecialchars($field->name) . "</th>";
                }
                echo "</tr>";

                // Display rows of data
                while ($row_data = mysqli_fetch_assoc($result_data)) {
                    echo "<tr>";
                    foreach ($row_data as $cell) {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No data found in this table.</p>";
            }
            mysqli_free_result($result_data);
        } else {
            echo "ERROR: Could not execute $sql_data. " . mysqli_error($dbconnection);
        }
    }
    mysqli_free_result($result_tables);
} else {
    echo "ERROR: Could not execute $sql_tables. " . mysqli_error($dbconnection);
}

// Close the database connection
mysqli_close($dbconnection);
?>
