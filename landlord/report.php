<?php include('header.php'); ?>

<?php
// Initialize variables
$total_income = 0;

// Fetch data from the rental table and join with the book table
$query = "
    SELECT r.rental_id AS rental_id, r.title, r.monthly, b.name AS broker_name
    FROM rental AS r
    LEFT JOIN book AS b ON r.rental_id = b.bhouse_id AND b.status = 'Approved'
    WHERE r.id = '$login_session'
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
            'has_approved_booking' => false,
            'total_monthly' => 0  // Initialize total monthly rent accumulator
        ];
    }

    if ($broker_name) {
        $rows[$rental_id]['brokers'][] = [
            'name' => $broker_name,
            'monthly' => $monthly
        ];
        $rows[$rental_id]['has_approved_booking'] = true; // Mark that there is an approved booking
        $rows[$rental_id]['total_monthly'] += $monthly; // Accumulate monthly rent for this boarding house
        $total_income += $monthly; // Sum each broker's monthly rent to the total income
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
        'monthly_details' => $monthly_details,
        'total_monthly' => $data['total_monthly']  // Include total monthly rent for this boarding house
    ];
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
                <img src="../bh.jpg" alt="Logo" />
            </div>
            <div class="print-text">
                <h2>Madridejos Boarding House Finder</h2>
            </div>
        </div>
        <br><br>
        <h3>
            Monthly Report
            <button class="btn btn-primary btn-print" style="float: right;" onclick="window.print()">Print</button>
        </h3>
        <br />
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Boarders Name</th>
                    <th>Monthly Rent</th>
                    <th>Total Monthly Rent</th> <!-- New column for total monthly rent per boarding house -->
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
                        <td>₱<?php echo number_format($row['total_monthly'], 2); ?></td> <!-- Display total monthly rent -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div>
            <strong style=" margin-left: 1120px";>Total Income: ₱<?php echo number_format($total_income, 2); ?></strong>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
