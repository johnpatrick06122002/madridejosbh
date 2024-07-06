<?php include('header.php'); ?>

<?php
// Initialize variables
$total_income = 0;

// Fetch data from the rental table and join with the book and landlord tables
$query = "
    SELECT r.rental_id AS rental_id, r.title, r.monthly, l.name AS landlord_name, b.name AS broker_name
    FROM rental AS r
    LEFT JOIN book AS b ON r.rental_id = b.bhouse_id AND b.status = 'Approved'
    LEFT JOIN landlords AS l ON r.id = l.id
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
    $monthly = (float)$row['monthly'];  // Ensure monthly is treated as a number
    $landlord_name = htmlspecialchars($row['landlord_name']);
    $broker_name = htmlspecialchars($row['broker_name']);

    // Organize the data by rental ID
    if (!isset($rows[$rental_id])) {
        $rows[$rental_id] = [
            'title' => $title,
            'landlord' => $landlord_name,
            'brokers' => [],
            'total_monthly' => 0
        ];
    }

    if ($broker_name) {
        $rows[$rental_id]['brokers'][] = $broker_name;
        $rows[$rental_id]['total_monthly'] += $monthly;
        $total_income += $monthly;
    }
}

// Prepare rows for display
$display_rows = [];
foreach ($rows as $rental_id => $data) {
    $broker_count = count($data['brokers']);
    $broker_names = implode(', ', $data['brokers']);
    $total_monthly_rent = '₱' . number_format($data['total_monthly'], 2);
    $display_rows[] = [
        'title' => $data['title'],
        'landlord' => $data['landlord'],
        'brokers' => $broker_count,
        'broker_names' => $broker_names,
        'total_monthly' => $total_monthly_rent
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
        <h3>
            Admin Monthly Report
            <button class="btn btn-primary btn-print" style="float: right;" onclick="window.print()">Print</button>
        </h3>
        <br />
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Boarding House</th>
                    <th>Landlord</th>
                    <th>Number of Brokers</th>
                    <th>Broker Names</th>
                    <th>Total Monthly Rent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($display_rows as $row): ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['landlord']; ?></td>
                        <td><?php echo $row['brokers']; ?></td>
                        <td><?php echo $row['broker_names']; ?></td>
                        <td><?php echo $row['total_monthly']; ?></td>
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
