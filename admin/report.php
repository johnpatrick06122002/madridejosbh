<?php
include('../connection.php');

// Get the start and end date for the selected interval (defaults to this month)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] . '-01' : date('Y-m-01');  // Default to first day of the current month
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] . '-31' : date('Y-m-t');  // Default to last day of the current month

// SQL query to count bookings for the selected interval and show associated boarding house information
$query = "SELECT 
                COUNT(b.id) AS booking_count,
                MONTH(b.date_posted) AS month,
                YEAR(b.date_posted) AS year,
                r.title AS boarding_house_title
          FROM `book` b
          JOIN `rental` r ON b.bhouse_id = r.rental_id  -- Join on rental_id
          WHERE b.date_posted BETWEEN ? AND ? 
          GROUP BY YEAR(b.date_posted), MONTH(b.date_posted), r.title
          ORDER BY YEAR(b.date_posted) DESC, MONTH(b.date_posted) DESC";

// Prepare and bind
$stmt = $dbconnection->prepare($query);
$stmt->bind_param("ss", $start_date, $end_date);  // Binding the start and end dates
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Main content container -->
<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="main-content">
        <h3>Booking Report</h3>
        <div class="content">
            <p>Showing bookings from <strong><?php echo $start_date; ?></strong> to <strong><?php echo $end_date; ?></strong></p>
            
            <!-- Report form to select start and end dates -->
            <form method="get" class="report-form">
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="month" name="start_date" id="start_date" value="<?php echo date('Y-m', strtotime($start_date)); ?>" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="month" name="end_date" id="end_date" value="<?php echo date('Y-m', strtotime($end_date)); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </form>

            <!-- Print Button -->
            <button onclick="printReport()" class="btn btn-info">Print Report</button>

            <!-- Display the report table -->
            <div class="report-table">
                <?php
                if ($result->num_rows > 0) {
                    echo "<table class='table table-striped' id='report-table'>";
                    echo "<thead>
                            <tr>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Boarding House</th>
                                <th>Booking Count</th>
                            </tr>
                          </thead>
                          <tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . date('F', mktime(0, 0, 0, $row['month'], 10)) . "</td>
                                <td>{$row['year']}</td>
                                <td>{$row['boarding_house_title']}</td>
                                <td>{$row['booking_count']}</td>
                              </tr>";
                    }

                    echo "</tbody></table>";
                } else {
                    echo "<p>No bookings found for the selected period.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Close connection -->
<?php
$stmt->close();
$dbconnection->close();
?>

<!-- CSS Styling -->
<style>

/* Main layout container */
.dashboard-container {
    display: flex;
    min-height: 100vh;
    width: 100%;
}

/* Sidebar styles */
.sidebar-container {
    width: 250px;
    background: #fff;
    border-right: 1px solid #e3e6f0;
    flex-shrink: 0;
}

/* Main content area */
.main-content {
    flex-grow: 1;
    padding: 20px;
    background: #f8f9fc;
    overflow-x: hidden;
}
/* Form styles */
.report-form {
    margin-bottom: 20px;
}

.report-form .form-group {
    margin-bottom: 15px;
}

.report-form label {
    font-weight: bold;
}

.report-form input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.report-form button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.report-form button:hover {
    background-color: #45a049;
}

/* Print button styles */
.btn-info {
    padding: 10px 20px;
    background-color: #17a2b8;
    color: white;
    border: none;
    border-radius: 4px;
    margin-bottom: 20px;
    cursor: pointer;
}

.btn-info:hover {
    background-color: #138496;
}

/* Table styles */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.table th, .table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #f2f2f2;
    font-weight: bold;
}

.table tr:hover {
    background-color: #f5f5f5;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dashboard-container {
        flex-direction: column;
    }

    .sidebar-container {
        width: 100%;
        position: static;
        height: auto;
    }

    .main-content {
        padding: 15px;
    }
}
</style>

<!-- JavaScript for printing the report -->
<script>
function printReport() {
    var printContents = document.getElementById('report-table').outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>
