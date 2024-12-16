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

// Query to create the 'paid' table
$sql_create_paid = "CREATE TABLE IF NOT EXISTS `paid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `last_date_pay` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

// Execute the query to create the 'paid' table
if (mysqli_query($dbconnection, $sql_create_paid)) {
    echo "Table 'paid' created (if not already exists).<br>";
} else {
    echo "ERROR: Could not create 'paid' table. " . mysqli_error($dbconnection) . "<br>";
}

// Query to fetch all tables from the database
$sql = "SHOW TABLES";
$result = mysqli_query($dbconnection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($dbconnection));
}

// Display tables and their data
echo "<h2>Database Tables</h2>";

while ($table = mysqli_fetch_row($result)) {
    $tableName = $table[0];
    echo "<h3>Table: $tableName</h3>";

    // Query to fetch all rows from the current table
    $dataQuery = "SELECT * FROM $tableName";
    $dataResult = mysqli_query($dbconnection, $dataQuery);

    if ($dataResult) {
        // Fetch column names dynamically
        $fields = mysqli_fetch_fields($dataResult);

        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<thead><tr>";

        // Display column headers
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }

        echo "</tr></thead><tbody>";

        // Display table data
        while ($row = mysqli_fetch_assoc($dataResult)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }

        echo "</tbody></table><br>";
        // Free result set for this table
        mysqli_free_result($dataResult);
    } else {
        echo "ERROR: Could not fetch data from $tableName.<br>";
    }
}

// Free result set for tables list and close the connection
mysqli_free_result($result);
mysqli_close($dbconnection);
?>
