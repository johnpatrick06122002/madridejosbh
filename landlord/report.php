<?php include('header.php'); ?>
<?php
 
require_once '../connection.php'; // Ensure database connection file is included

// Redirect to login if not authenticated
if (!isset($_SESSION['login_user'])) {
    header("location:../login.php");
    die();
}

$total_income = 0;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Sanitize and validate inputs
$login_session = $_SESSION['login_user'];
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
    die("Invalid date format.");
}

try {
    $stmt = $dbconnection->prepare("
        SELECT 
            r.rental_id, r.title, r.monthly, 
            b.firstname, b.lastname, b.status, 
            p.amount AS paid_amount, p.last_date_pay
        FROM rental AS r
        LEFT JOIN payment AS p ON r.rental_id = p.rental_id
        LEFT JOIN booking AS b ON p.payment_id = b.payment_id
        WHERE b.status = 'Confirm'
          AND r.register1_id = ?
          AND (
              (p.last_date_pay BETWEEN ? AND ?)
              OR (b.date_posted BETWEEN ? AND ?)
              OR (p.last_date_pay IS NULL AND b.date_posted <= ?)
          )
    ");
    $stmt->bind_param("ssssss", $login_session, $start_date, $end_date, $start_date, $end_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rental_id = $row['rental_id'];
        $title = htmlspecialchars($row['title']);
        $paid_amount = floatval($row['paid_amount']);
        $firstname = htmlspecialchars($row['firstname']);
        $lastname = htmlspecialchars($row['lastname']);

        if (!isset($rows[$rental_id])) {
            $rows[$rental_id] = [
                'title' => $title,
                'boarders' => [],
                'total_paid_amount' => 0,
            ];
        }

        if ($firstname && $lastname) {
            $rows[$rental_id]['boarders'][] = [
                'name' => "$firstname $lastname",
                'paid_amount' => $paid_amount,
            ];
            $rows[$rental_id]['total_paid_amount'] += $paid_amount;
            $total_income += $paid_amount;
        }
    }

    $stmt->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Prepare rows for display
$display_rows = [];
foreach ($rows as $rental_id => $data) {
    $boarder_details = '';
    $paid_amount_details = '';
    foreach ($data['boarders'] as $boarder) {
        $boarder_details .= '<tr><td>' . $boarder['name'] . '</td></tr>';
        $paid_amount_details .= '<tr><td>₱' . number_format($boarder['paid_amount'], 2) . '</td></tr>';
    }
    $display_rows[] = [
        'title' => $data['title'],
        'boarder_details' => $boarder_details,
        'paid_amount_details' => $paid_amount_details,
        'total_paid_amount' => $data['total_paid_amount'],
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
@media print {
    .print-logo {
        display: block !important; /* Show logo only during print */
        margin: 0 auto 20px;
        width: 100px; /* Adjust width as needed */
    }
}
        /* Print-specific styles */
@media print {
    /* Reset page margins and ensure full content printing */
    @page {
        margin: 0.5in;
        size: portrait;
    }

    body {
        font-family: Arial, sans-serif;
        line-height: 1.4;
        color: #000;
        background: #fff;
    }

    /* Hide non-printable elements */
    .sidebar-container, .btn-print, .btn-download, form {
        display: none !important;
    }

    /* Show and style the logo */
    .print-logo {
        display: block !important;
        margin: 0 auto 30px;
        width: 120px;
        height: auto;
    }

    /* Header styling */
    .print-title {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 30px;
        border-bottom: 2px solid #333;
        padding-bottom: 10px;
    }

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    th {
        background-color: #f5f5f5 !important;
        color: #333;
        font-weight: bold;
        padding: 12px 8px;
        border: 1px solid #ddd;
        text-align: left;
    }

    td {
        padding: 10px 8px;
        border: 1px solid #ddd;
        text-align: left;
    }

    /* Nested tables styling */
    td table {
        margin: 0;
    }

    td table td {
        border: none;
        padding: 4px 0;
    }

    /* Total amount styling */
    strong {
        display: block;
        margin: 20px 0;
        font-size: 16px;
        color: #000;
        text-align: right;
        padding-right: 20px;
    }

    /* Main content area */
    .main-content {
        padding: 0;
        width: 100%;
        margin: 0 auto;
    }

    .printable-report {
        max-width: 100%;
        margin: 0 auto;
    }

    /* Header styling */
    h3 {
        text-align: center;
        font-size: 22px;
        margin: 20px 0;
        color: #333;
    }

    /* Ensure good page breaks */
    h3, table, .printable-report {
        page-break-before: auto;
    }

    /* Add subtle zebra striping */
    tr:nth-child(even) {
        background-color: #f9f9f9 !important;
    }

    /* Print background colors */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
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
  
/* Table styles */
.table {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    font-size: 14px;
}

.table thead th {
    background: #007bff;
    color: #fff;
    text-align: center;
}

.table tbody td {
    vertical-align: middle;
    text-align: center;
    padding: 10px;
}
    </style>
</head>
 <body>
<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>
    <div class="main-content">
        <h3>
            Monthly Report
            <button class="btn btn-primary btn-print" onclick="window.print()">Print</button>
            <button class="btn btn-primary btn-download" onclick="generatePDF()">Download PDF</button>
        </h3>
        <form method="GET" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>" required>
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>" required>
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </form>

        <div class="printable-report">
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
                                <td><table><?php echo $row['boarder_details']; ?></table></td>
                                <td><table><?php echo $row['paid_amount_details']; ?></table></td>
                                <td>₱<?php echo number_format($row['total_paid_amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div>
                <strong>Total Income: ₱<?php echo number_format($total_income, 2); ?></strong>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        // Set initial position
        let yPos = 20;

        // Add logo (assuming b.png is your logo)
        const logoPath = '../b.png';
        doc.addImage(logoPath, 'PNG', doc.internal.pageSize.getWidth() / 2 - 25, yPos, 50, 50);
        
        yPos += 60; // Space after logo

        // Set font styles
        doc.setFont('helvetica');
        
        // Add title with styling
        doc.setFontSize(24);
        doc.setFont('helvetica', 'bold');
        doc.text("Monthly Report", doc.internal.pageSize.getWidth() / 2, yPos, { align: 'center' });
        
        // Add horizontal line
        doc.setLineWidth(0.5);
        yPos += 5;
        doc.line(20, yPos, 190, yPos);

        // Set default font size for content
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');

        // Add total income with styling
        yPos += 15;
        doc.setFont('helvetica', 'bold');
        doc.text("Total Income: ₱<?php echo number_format($total_income, 2); ?>", 20, yPos);
        
        // Create table header
        yPos += 20;
        const headers = ['Title', 'Total Paid Amount'];
        const columnWidths = [100, 70];
        
        // Draw table header background
        doc.setFillColor(245, 245, 245);
        doc.rect(20, yPos - 5, 170, 10, 'FD');
        
        // Add table headers
        doc.setFont('helvetica', 'bold');
        headers.forEach((header, i) => {
            doc.text(header, 25 + (i * columnWidths[0]), yPos);
        });
        
        // Add data rows with borders
        <?php foreach ($display_rows as $index => $row): ?>
            yPos += 15;
            
            // Check if we need a new page
            if (yPos > 270) {
                doc.addPage();
                yPos = 20;
                
                // Redraw headers on new page
                doc.setFillColor(245, 245, 245);
                doc.rect(20, yPos - 5, 170, 10, 'FD');
                doc.setFont('helvetica', 'bold');
                headers.forEach((header, i) => {
                    doc.text(header, 25 + (i * columnWidths[0]), yPos);
                });
                yPos += 15;
            }
            
            // Draw cell borders
            doc.rect(20, yPos - 5, columnWidths[0], 10);
            doc.rect(20 + columnWidths[0], yPos - 5, columnWidths[1], 10);
            
            // Add cell content
            doc.setFont('helvetica', 'normal');
            doc.text("<?php echo $row['title']; ?>", 25, yPos);
            doc.text("₱<?php echo number_format($row['total_paid_amount'], 2); ?>", 25 + columnWidths[0], yPos);
        <?php endforeach; ?>

        // Add footer
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(10);
            doc.text(`Page ${i} of ${pageCount}`, doc.internal.pageSize.getWidth() / 2, 290, { align: 'center' });
        }

        // Save the PDF
        doc.save("monthly-report.pdf");
    }
</script>

<?php include('footer.php'); ?>
</body>
</html>