<?php
session_start(); // Start session at the beginning of the script

// Check if the admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit; // Stop further execution
}

include('header.php'); // Include header.php which contains necessary HTML and PHP code
?>

<?php
// Initialize variables
$total_income = 0;

// Fetch data from the rental table and join with the book and landlord tables
$query = "
    SELECT r.rental_id AS rental_id, r.title, r.monthly, l.name AS landlord_name, b.name AS broker_name
    FROM rental AS r
    LEFT JOIN book AS b ON r.rental_id = b.bhouse_id AND b.status = 'Approved'
    LEFT JOIN landlords AS l ON r.landlord_id = l.id
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
    $landlord_name = htmlspecialchars($row['landlord_name']);
    $broker_name = htmlspecialchars($row['broker_name']);

    // Organize the data by rental ID
    if (!isset($rows[$rental_id])) {
        $rows[$rental_id] = [
            'title' => $title,
            'landlord' => $landlord_name,
            'monthly' => $monthly,
            'broker_count' => 0,
            'total_monthly' => 0
        ];
    }

    if ($broker_name) {
        $rows[$rental_id]['broker_count']++;
        $rows[$rental_id]['total_monthly'] += $monthly;
        $total_income += $monthly;
    }
}

// Prepare rows for display
$display_rows = [];
foreach ($rows as $rental_id => $data) {
    if ($data['broker_count'] > 0) {
        $total_monthly_rent = '₱' . number_format($data['monthly'] * $data['broker_count'], 2);
        $display_rows[] = [
            'title' => $data['title'],
            'landlord' => $data['landlord'],
            'brokers' => $data['broker_count'],
            'monthly_rent' => '₱' . number_format($data['monthly'], 2),
            'total_monthly' => $total_monthly_rent
        ];
    }
}
?>

<style>
    .print-logo, .print-text {
        display: none;
        margin-right: 200px;
    }

    @media print {
        .sidebar, .btn-print {
            display: none;
        }
        .print-header {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .print-logo {
            margin-right: 20px;
        }
        .print-logo, .print-text {
            display: block;
        }

        .print-logo img {
            width: 100px;
            height: 100px;
        }
    }
    .btn {
        margin-right: 40px;
        width: 90px;
    }
</style>

<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>
    <div class="col-sm-10">
        <br />
        <div class="print-header">
            <div class="print-logo">
               <img src="../bhh.jpg" alt="Logo" />
            </div>
            <div class="print-text">
                <h2>Madridejos Boarding House Finder</h2>
            </div>
        </div>
        <br><br><br><br>
        <h3>
            Monthly Report
            <button class="btn btn-primary btn-print" style="float: right;" onclick="window.print()">Print</button>
        </h3>
        <br />
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Boarding House</th>
                    <th>Landlord</th>
                    <th>Number of Brokers</th>
                    <th>Monthly Rent</th>
                    <th>Total Monthly Rent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($display_rows as $row): ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['landlord']; ?></td>
                        <td><?php echo $row['brokers']; ?></td>
                        <td><?php echo $row['monthly_rent']; ?></td>
                        <td><?php echo $row['total_monthly']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div>
            <strong style="margin-left: 1190px;">Total Income: ₱<?php echo number_format($total_income, 2); ?></strong>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
