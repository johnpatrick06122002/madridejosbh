<?php
include('../connection.php');
session_start(); // Start session at the beginning of the script
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit; // Stop further execution
}
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
        <h3 class="page-title">Report</h3>
        <div class="content">
            <p class="date-range">Showing bookings from <strong><?php echo $start_date; ?></strong> to <strong><?php echo $end_date; ?></strong></p>

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
    background: #f4f7fc;
}

/* Sidebar styles */
.sidebar-container {
    width: 250px;
    background: #fff;
    border-right: 1px solid #e3e6f0;
    flex-shrink: 0;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Main content area */
.main-content {
    flex-grow: 1;
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
    border-radius: 8px;
    margin-left: 20px;
}

/* Page Title */
.page-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
}

/* Date Range Text */
.date-range {
    font-size: 16px;
    color: #777;
    margin-bottom: 20px;
}

/* Form styles */
.report-form {
    margin-bottom: 20px;
    display: flex;
    gap: 20px;
    align-items: center;
}

.report-form .form-group {
    margin-bottom: 15px;
    width: 100%;
}

.report-form label {
    font-weight: bold;
    color: #555;
}

.report-form input {
    width: 200px;
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
    transition: background-color 0.3s ease;
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
    transition: background-color 0.3s ease;
}

.btn-info:hover {
    background-color: #138496;
}

/* Table styles */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
    border-radius: 8px;
    overflow: hidden;
}

.table th, .table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #f2f2f2;
    font-weight: bold;
    color: #444;
}

.table tr:hover {
    background-color: #f5f5f5;
}

/* Table row hover effect */
.table tr:hover {
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
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
        margin-left: 0;
    }

    .report-form {
        flex-direction: column;
        align-items: flex-start;
    }

    .report-form button {
        margin-top: 10px;
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
