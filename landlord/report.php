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
<style>
       @media screen and (max-width: 700px) {
    .sidebar a {
       float: revert-layer !important;  
    }
}
    </style>
<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>
    <div class="col-sm-10">
        <br />
        <h3>
            Monthly Report
            <button class="btn btn-primary btn-print" style="float: right;" onclick="window.print()">Print</button>
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

<?php include('footer.php'); ?>
