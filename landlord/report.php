<?php include('header.php'); ?>

<?php
// Initialize variables
$total_income = 0;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Default to the first day of the current month
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Default to today

// Sanitize input dates and session data
$start_date = mysqli_real_escape_string($dbconnection, $start_date); // Assuming $dbconnection is your database connection
$end_date = mysqli_real_escape_string($dbconnection, $end_date);
$login_session = mysqli_real_escape_string($dbconnection, $login_session);

// Fetch data from the rental table and join with the book table, filtering by date range and status 'Confirm'
$query = "
    SELECT r.rental_id, r.title, r.monthly, b.firstname AS broker_name, b.paid_amount, b.last_payment_date
    FROM rental AS r
    LEFT JOIN book AS b ON r.rental_id = b.bhouse_id
    WHERE b.status = 'Confirm'
    AND b.register1_id = '$login_session' -- Filter by specific boarding house
    AND (
        (b.last_payment_date BETWEEN '$start_date' AND '$end_date')
        OR (b.date_posted BETWEEN '$start_date' AND '$end_date')
        OR (b.last_payment_date IS NULL AND b.date_posted <= '$end_date')
    )
";

// Execute the query
$result = mysqli_query($dbconnection, $query);

if (!$result) {
    die("Error: " . mysqli_error($dbconnection));
}

$rows = [];

// Fetch data and organize it
while ($row = mysqli_fetch_assoc($result)) {
    $rental_id = $row['rental_id'];
    $title = htmlspecialchars($row['title']);
    $paid_amount = floatval($row['paid_amount']);
    $broker_name = htmlspecialchars($row['broker_name']);

    // Organize the data by rental ID
    if (!isset($rows[$rental_id])) {
        $rows[$rental_id] = [
            'title' => $title,
            'brokers' => [],
            'has_confirmed_booking' => false,
            'total_paid_amount' => 0
        ];
    }

    if ($broker_name) {
        $rows[$rental_id]['brokers'][] = [
            'name' => $broker_name,
            'paid_amount' => $paid_amount
        ];
        $rows[$rental_id]['has_confirmed_booking'] = true;
        $rows[$rental_id]['total_paid_amount'] += $paid_amount;
        $total_income += $paid_amount;
    }
}

// Prepare rows for display
$display_rows = [];
foreach ($rows as $rental_id => $data) {
    $broker_details = '';
    $paid_amount_details = '';
    foreach ($data['brokers'] as $broker) {
        $broker_details .= '<tr><td>' . $broker['name'] . '</td></tr>';
        $paid_amount_details .= '<tr><td>₱' . number_format($broker['paid_amount'], 2) . '</td></tr>';
    }
    $display_rows[] = [
        'title' => $data['title'],
        'broker_details' => $broker_details,
        'paid_amount_details' => $paid_amount_details,
        'total_paid_amount' => $data['total_paid_amount']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Add viewport for responsive design -->
    <title>Monthly Report</title>
    <style>
        /* General mobile layout adjustments */
        @media screen and (max-width: 700px) {
            .sidebar a {
                float: revert-layer !important;  
            }
        }

        h3 {
            margin-left: 10px;
        }

        /* Print-specific styles */
        @media print {
            /* Hide sidebar and unnecessary buttons for print */
            .sidebar, .btn-print, form {
                display: none;
            }

            /* Adjust layout for printing */
            .col-sm-10 {
                width: 100%;
                margin: 0 auto;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid black;
            }

            th, td {
                padding: 10px;
                text-align: left;
            }

            /* Make font sizes larger for better readability */
            body {
                font-size: 14px;
            }

            /* Ensure titles and content fit properly */
            h3 {
                text-align: center;
                font-size: 18px;
            }

            strong {
                font-size: 16px;
                margin-left: 0;
            }
        }

        /* Show Download PDF button and hide Print button on mobile view (screen width <= 700px) */
        @media screen and (max-width: 700px) {
            .btn-print {
                display: none; /* Hide the Print button on mobile */
            }
            .btn-download {
                display: inline-block; /* Show the Download PDF button on mobile */
            }
        }

        /* Hide Download PDF button and show Print button on desktop view (screen width > 700px) */
        @media screen and (min-width: 701px) {
            .btn-print {
                display: inline-block; /* Show the Print button on desktop */
            }
            .btn-download {
                display: none; /* Hide the Download PDF button on desktop */
            }
        }
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

/* Dashboard cards row */
.dashboard-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
}

/* Card styles */
.card-box {
    flex: 1;
    min-width: 240px;
    background-color: #ffffff;
    border: 1px solid #e3e6f0;
    border-radius: 5px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    padding: 20px;
}

/* Chart containers */
.chart-container1, .chart-container2, .chart-container3 {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    margin-bottom: 30px;
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
    
    .card-box {
        min-width: 100%;
    }
    
    .chart-container1, .chart-container2, .chart-container3 {
        width: 100% !important;
        height: 300px !important;
    }
}

/* Existing styles with improvements */
.widget-style3 {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-data {
    flex-grow: 1;
}

.widget-icon {
    margin-left: 15px;
}

.font-24 {
    font-size: 20px !important;
}

.animated-icon {
    animation: pulse 1.3s infinite;
}

@keyframes pulse {
    0% { transform: scale(1.5); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

h3 {
    margin: 0 0 20px 0;
    color: #5a5c69;
    font-weight: 500;
}
    </style>
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>
   
    <div class="main-content"> <br><br><br>
        <h3>
            Monthly Report
            <button class="btn btn-primary btn-print" style="float: right;" onclick="window.print()">Print</button>
            <button class="btn btn-primary btn-download" style="float: right; " onclick="generatePDF()">Download PDF</button>
        </h3>
        <br />

        <!-- Date Range Selection Form -->
        <form method="GET" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>" required>
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>" required>
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </form>
        <br />

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Boarders Name</th>
                    <th>Paid Amount</th>
                    <th>Total Paid Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($display_rows)): ?>
                    <tr><td colspan="4">No records found for the selected date range.</td></tr>
                <?php else: ?>
                    <?php foreach ($display_rows as $row): ?>
                        <tr>
                            <td><?php echo $row['title']; ?></td>
                            <td>
                                <table>
                                    <?php echo $row['broker_details']; ?>
                                </table>
                            </td>
                            <td>
                                <table>
                                    <?php echo $row['paid_amount_details']; ?>
                                </table>
                            </td>
                            <td>₱<?php echo number_format($row['total_paid_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Total Income -->
        <div>
            <strong style="margin-left: 20px;">Total Income: ₱<?php echo number_format($total_income, 2); ?></strong>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Add title to the PDF
        doc.text("Monthly Report", 10, 10);

        // Add additional report data
        doc.text("Total Income: ₱<?php echo number_format($total_income, 2); ?>", 10, 20);

        // You can loop through the data and add more details if needed
        <?php foreach ($display_rows as $index => $row): ?>
        doc.text("Title: <?php echo $row['title']; ?>", 10, <?php echo ($index + 1) * 30; ?>);
        doc.text("Total Paid Amount: ₱<?php echo number_format($row['total_paid_amount'], 2); ?>", 10, <?php echo ($index + 1) * 35; ?>);
        <?php endforeach; ?>

        // Save the PDF
        doc.save("monthly-report.pdf");
    }
</script>
<?php include('footer.php'); ?>
</body>
</html>
