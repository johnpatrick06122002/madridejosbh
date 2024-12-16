<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement of Account</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .container {
            max-width: 800px;
            padding: 2rem 1rem;
        }

        .page-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 600;
        }

        .search-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .booking-details {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .details-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .payment-table {
            width: 100%;
            margin-top: 1rem;
        }

        .payment-table th,
        .payment-table td {
            padding: 1rem;
            text-align: left;
        }

        .payment-table thead {
            background-color: #f8f9fa;
        }

        .payment-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .search-form,
            .booking-details {
                padding: 1.5rem;
            }

            .payment-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <?php
    require_once 'connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_ref_no'])) {
        $bookRefNo = $_POST['book_ref_no'];
        
        $query = "SELECT 
                    b.book_ref_no,
                    b.status,
                    b.date_posted,
                    r.title AS boarding_house,
                    r.address,
                    py.payment_id
                  FROM booking b
                  LEFT JOIN payment py ON b.payment_id = py.payment_id
                  LEFT JOIN rental r ON py.rental_id = r.rental_id
                  WHERE b.book_ref_no = ?";
        
        $stmt = $dbconnection->prepare($query);
        if ($stmt === false) {
            die("Error preparing the statement: " . $dbconnection->error);
        }
        $stmt->bind_param("s", $bookRefNo);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();
        
        $paidRecords = [];
        if ($booking && $booking['payment_id']) {
            $paidQuery = "SELECT amount, last_date_pay 
                          FROM paid 
                          WHERE payment_id = ?";
            $paidStmt = $dbconnection->prepare($paidQuery);
            if ($paidStmt === false) {
                die("Error preparing the statement: " . $dbconnection->error);
            }
            $paidStmt->bind_param("i", $booking['payment_id']);
            $paidStmt->execute();
            $paidResult = $paidStmt->get_result();
            $paidRecords = $paidResult->fetch_all(MYSQLI_ASSOC);
        }
    }
    ?>

    <div class="container">
        <h1 class="page-title">Statement of Account</h1>
        
        <div class="search-form">
            <form method="POST" action="soa.php" class="row g-3 align-items-center">
                <div class="col-md-8">
                    <label for="book_ref_no" class="form-label">Enter Booking Reference Number:</label>
                    <input type="text" id="book_ref_no" name="book_ref_no" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
        
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($booking)): ?>
            <div class="booking-details">
                <?php if (!$booking): ?>
                    <div class="alert alert-warning">
                        No booking found with the reference number "<?php echo htmlspecialchars($bookRefNo); ?>"
                    </div>
                <?php else: ?>
                    <?php if ($booking['status'] === 'Pending'): ?>
                        <div class="status-badge status-pending">
                            Booking Status: Pending
                        </div>
                        <div class="details-card">
                            <p class="mb-2"><strong>Booking Date:</strong> <?php echo htmlspecialchars($booking['date_posted']); ?></p>
                            <p class="text-muted mb-0">Your booking is currently under review.</p>
                        </div>
                    <?php elseif ($booking['status'] === 'Confirm'): ?>
                        <div class="status-badge status-confirmed">
                            Booking Status: Confirmed
                        </div>
                        <div class="details-card">
                            <p class="mb-2"><strong>Boarding House:</strong> <?php echo htmlspecialchars($booking['boarding_house']); ?></p>
                            <p class="mb-0"><strong>Address:</strong> <?php echo htmlspecialchars($booking['address']); ?></p>
                        </div>
                        
                        <h3 class="h4 mb-3">Payment Records</h3>
                        <?php if (!empty($paidRecords)): ?>
                            <div class="table-responsive">
                                <table class="table payment-table">
                                    <thead>
                                        <tr>
                                            <th>Amount</th>
                                            <th>Payment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($paidRecords as $record): ?>
                                            <tr>
                                                <td>₱<?php echo number_format(htmlspecialchars($record['amount']), 2); ?></td>
                                                <td><?php echo date('F j, Y', strtotime($record['last_date_pay'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No payments found for this booking.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            Unknown booking status.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>