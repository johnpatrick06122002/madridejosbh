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

// Handle Delete Action
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($dbconnection, $_POST['id']);
    $delete_sql = "DELETE FROM register1 WHERE id = '$id'";
    if (mysqli_query($dbconnection, $delete_sql)) {
        echo "<div style='color: green; padding: 10px;'>Record deleted successfully.</div>";
    } else {
        echo "<div style='color: red; padding: 10px;'>Error deleting record: " . mysqli_error($dbconnection) . "</div>";
    }
}

// Establish the database connection
$dbconnection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check the connection
if ($dbconnection === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Add CSS for styling
echo "
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        margin: 20px 0;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f4f4f4;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .delete-btn {
        background-color: #ff4444;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }
    .delete-btn:hover {
        background-color: #cc0000;
    }
</style>";

// Query to describe the table structure
$describe_sql = "DESCRIBE register2";
$describe_result = mysqli_query($dbconnection, $describe_sql);

if ($describe_result) {
    echo "<h3>Table Structure: register</h3>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

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
$data_sql = "SELECT * FROM register2";
$data_result = mysqli_query($dbconnection, $data_sql);

if ($data_result) {
    if (mysqli_num_rows($data_result) > 0) {
        echo "<h3>Data in the register1 Table</h3>";
        echo "<table>";

        // Fetch and display column headers dynamically
        $fields = mysqli_fetch_fields($data_result);
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "<th>Action</th>"; // Add Action column
        echo "</tr>";

        // Fetch and display data rows
        while ($row = mysqli_fetch_assoc($data_result)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            // Add delete button with confirmation
            echo "<td>
                    <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this record?\");' style='display: inline;'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                        <button type='submit' name='delete' class='delete-btn'>Delete</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "The register1 table is empty.";
    }
} else {
    echo "ERROR: Could not execute $data_sql. " . mysqli_error($dbconnection);
}

// Close the database connection
mysqli_close($dbconnection);
?>