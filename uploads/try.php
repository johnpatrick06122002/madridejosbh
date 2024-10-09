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

// Query to fetch all data from the 'book' table
$query = "SELECT * FROM book";
$result = mysqli_query($dbconnection, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<h1>Data in the 'book' table:</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>First Name</th><th>Middle Name</th><th>Last Name</th><th>Email</th><th>Age</th><th>Gender</th><th>Contact Number</th><th>Address</th><th>GCash Picture</th><th>Register ID</th><th>Bhouse ID</th><th>Status</th><th>Ratings</th><th>Feedback</th><th>Balance</th><th>Date Posted</th><th>Paid Amount</th><th>Rental Months</th><th>Due Amount</th><th>Last Payment Date</th></tr>";

    // Fetch and display each row of data
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['firstname'] . "</td>";
        echo "<td>" . $row['middlename'] . "</td>";
        echo "<td>" . $row['lastname'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['age'] . "</td>";
        echo "<td>" . $row['gender'] . "</td>";
        echo "<td>" . $row['contact_number'] . "</td>";
        echo "<td>" . $row['Address'] . "</td>";
        echo "<td>" . $row['gcash_picture'] . "</td>";
        echo "<td>" . $row['register1_id'] . "</td>";
        echo "<td>" . $row['bhouse_id'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['ratings'] . "</td>";
        echo "<td>" . $row['feedback'] . "</td>";
        echo "<td>" . $row['balance'] . "</td>";
        echo "<td>" . $row['date_posted'] . "</td>";
        echo "<td>" . $row['paid_amount'] . "</td>";
        echo "<td>" . $row['rental_months'] . "</td>";
        echo "<td>" . $row['due_amount'] . "</td>";
        echo "<td>" . $row['last_payment_date'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No data found in the 'book' table.";
}

// Close the connection
mysqli_close($dbconnection);
?>
