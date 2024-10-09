<?php include('header.php'); ?>

<?php
// Initialize variables
$total_income = 0;

// Fetch data from the rental table and join with the book table
$query = "
    SELECT r.rental_id AS rental_id, r.title, r.monthly, b.name AS broker_name
    FROM rental AS r
    LEFT JOIN book AS b ON r.rental_id = b.bhouse_id AND b.status = 'Approved'
    WHERE r.landlord_id = '$login_session'
";
$result = mysqli_query($dbconnection, $query);

if (!$result) {
    die("Error: " . mysqli_error($dbconnection));
}

$rows = [];

// Fetch data and organize it
while ($row = mysqli_fetch_assoc($result)) {
    $rental_id = $row['rental_id'];
    $title = htmlspecialchars($row['title']);
    $monthly = floatval($row['monthly']);  // Ensure monthly is treated as a float
    $broker_name = htmlspecialchars($row['broker_name']);

    // Organize the data by rental ID
    if (!isset($rows[$rental_id])) {
        $rows[$rental_id] = [
            'title' => $title,
            'brokers' => [],
            'has_approved_booking' => false
        ];
    }

    if ($broker_name) {
        $rows[$rental_id]['brokers'][] = [
            'name' => $broker_name,
            'monthly' => $monthly
        ];
        $rows[$rental_id]['has_approved_booking'] = true; // Mark that there is an approved booking
        $total_income += $monthly; // Sum each broker's monthly rent
    }
}

// Prepare rows for display
$display_rows = [];
foreach ($rows as $rental_id => $data) {
    $broker_details = '';
    $monthly_details = '';
    foreach ($data['brokers'] as $broker) {
        $broker_details .= '<tr><td>' . $broker['name'] . '</td></tr>';
        $monthly_details .= '<tr><td>₱' . number_format($broker['monthly'], 2) . '</td></tr>';
    }
    $display_rows[] = [
        'title' => $data['title'],
        'broker_details' => $broker_details,
        'monthly_details' => $monthly_details
    ];
}
?>

<style>
    @media print {
        .sidebar, .btn-print {
            display: none;
        }
    }
</style>

<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>
    <div class="col-sm-10">
        <br />
        <div style="text-align: center;">
            <img src="path/to/logo.png" alt="Logo" style="width: 150px;" />
            <h2>Your Company Name</h2>
            <p>Your company address, contact information, etc.</p>
        </div>
        <h3>
            Monthly Report
        </h3>
        <br />
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Broker Name</th>
                    <th>Monthly Rent</th>
                </tr>
            </thead>
            <tbody>
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
                                <?php echo $row['monthly_details']; ?>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div>
            <strong>Total Monthly Income: ₱<?php echo number_format($total_income, 2); ?></strong>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
