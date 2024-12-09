<?php
// Include database connection
include('../connection.php');

// Start session only if it hasn't started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has register1_id in session
if (!isset($_SESSION['register1_id'])) {
    header("Location: ../login.php");
    exit();
}

$current_user_id = $_SESSION['register1_id'];

// Function to fetch the monthly rental rate for a specific rental
function getMonthlyRateForRental($bhouseId) {
    global $dbconnection;

    $query = "SELECT monthly FROM rental WHERE rental_id = ?";
    $stmt = $dbconnection->prepare($query);

    if ($stmt === false) {
        die("MySQL error: " . $dbconnection->error);
    }

    $stmt->bind_param("i", $bhouseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['monthly'];
    }
    return 0;
}
function calculateBalance($id, $monthlyRate, $paidAmount) {
    global $dbconnection;

    $query = "SELECT last_payment_date FROM book WHERE id = ?";
    $stmt = $dbconnection->prepare($query);

    if ($stmt === false) {
        die("MySQL error: " . $dbconnection->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $lastPaymentDate = $row['last_payment_date'];
        $currentDate = date('Y-m-d');
        $dateDifference = (strtotime($currentDate) - strtotime($lastPaymentDate)) / (60 * 60 * 24);

        if ($dateDifference >= 30) {
            if ($paidAmount < $monthlyRate) {
                $balance = $monthlyRate - $paidAmount;
                return $balance + $monthlyRate;
            } else {
                return $monthlyRate;
            }
        } else {
            return $monthlyRate - $paidAmount;
        }
    }

    return $monthlyRate - $paidAmount;
}

// Check for payment or delete submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && isset($_POST['paid_amount'])) {
        $id = $_POST['id'];
        $paidAmount = $_POST['paid_amount'];

        // Verify that this booking belongs to the current user's rental
        $verifyQuery = "SELECT b.id 
                       FROM book b 
                       INNER JOIN rental r ON b.bhouse_id = r.rental_id 
                       WHERE b.id = ? AND r.register1_id = ?";
        $verifyStmt = $dbconnection->prepare($verifyQuery);
        $verifyStmt->bind_param("ii", $id, $current_user_id);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();

        if ($verifyResult->num_rows > 0) {
            // Update the paid amount
            $updateQuery = "UPDATE book SET paid_amount = paid_amount + ?, last_payment_date = CURRENT_DATE WHERE id = ?";
            $stmt = $dbconnection->prepare($updateQuery);
            $stmt->bind_param("di", $paidAmount, $id);
            $stmt->execute();
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];

        // Verify that this booking belongs to the current user's rental
        $verifyQuery = "SELECT b.id 
                       FROM book b 
                       INNER JOIN rental r ON b.bhouse_id = r.rental_id 
                       WHERE b.id = ? AND r.register1_id = ?";
        $verifyStmt = $dbconnection->prepare($verifyQuery);
        $verifyStmt->bind_param("ii", $delete_id, $current_user_id);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();

        if ($verifyResult->num_rows > 0) {
            // Delete the record
            $deleteQuery = "DELETE FROM book WHERE id = ?";
            $stmt = $dbconnection->prepare($deleteQuery);
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Include the header
include('header.php');

// Define the number of records per page
$results_per_page = 5;

// Determine the current page number
$pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;

// Calculate the offset for the SQL query
$offset = ($pageno - 1) * $results_per_page;

// Get the total number of records with status 'confirm' for current user's rentals
$total_pages_sql = "
    SELECT COUNT(*) 
    FROM book b
    INNER JOIN rental r ON b.bhouse_id = r.rental_id
    WHERE b.status = 'Confirm' 
    AND r.register1_id = ?";

$stmt_count = $dbconnection->prepare($total_pages_sql);
$stmt_count->bind_param("i", $current_user_id);
$stmt_count->execute();
$total_rows = $stmt_count->get_result()->fetch_array()[0];
$total_pages = ceil($total_rows / $results_per_page);

// Fetch the records for the current page
$query = "
    SELECT b.id, b.firstname, b.middlename, b.lastname, b.email, 
           b.age, b.gender, b.contact_number, b.Address, 
           b.date_posted, b.paid_amount, b.bhouse_id
    FROM book b
    INNER JOIN rental r ON b.bhouse_id = r.rental_id
    WHERE b.status = 'Confirm' 
    AND r.register1_id = ?
    LIMIT ?, ?";

$stmt = $dbconnection->prepare($query);
$stmt->bind_param("iii", $current_user_id, $offset, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();
?>
<style>
       
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

/* Table container */
.table-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    margin-bottom: 20px;
    overflow: hidden;
}

/* Table styles */
.table-responsive {
    margin: 0;
    padding: 0;
    width: 100%;
}

.table {
    margin-bottom: 0;
    width: 100%;
}

.table th {
    background: #f8f9fc;
    font-weight: 600;
    padding: 12px 15px;
    white-space: nowrap;
}

.table td {
    padding: 12px 15px;
    vertical-align: middle;
}

/* Button styles */
.action-buttons {
    display: flex;
    gap: 5px;
}

.btn {
    padding: 6px 12px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
}

.btn i {
    font-size: 14px;
}

 

/* Header styles */
h3 {
    margin: 0 0 20px 0;
    color: #5a5c69;
    font-weight: 500;
    font-size: 1.75rem;
}

/* Responsive styles */
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

    .table th, .table td {
        padding: 8px;
        font-size: 14px;
    }

    .btn {
        padding: 4px 8px;
        min-width: 30px;
    }

     

    h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .action-buttons {
        flex-direction: column;
        gap: 3px;
    }
}
    /* General table styles */
    .table {
        width: 100%;
        table-layout: auto;
    }

    /* Make sure table scrolls horizontally on smaller screens */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* For smooth scrolling on iOS */
    }

    /* Card layout for mobile */
    .card {
        border: 1px solid #ddd;
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 5px;
    }

    /* For screens larger than 700px (tablets, desktops) */
    @media screen and (min-width: 700px) {
        th, td {
            font-size: 14px;
            padding: 8px;
        }
    }

    /* For small screens (700px or less) */
    @media screen and (max-width: 700px) {
        .table {
            display: none; /* Hide the table */
        }

        /* Ensure content wraps inside cards */
        .card th, .card td {
            white-space: normal; /* Allow text wrapping */
            word-wrap: break-word;
        }

        /* Style for headers in card layout */
        .card-header {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px; /* Increased size for better visibility */
        }

        .card p {
            font-size: 14px; /* Increased size for better visibility */
            margin: 5px 0; /* Added margin for spacing */
        }
    }

    /* For very small screens (400px or less) */
    @media screen and (max-width: 400px) {
        .card {
            font-size: 10px; /* Even smaller font */
        }
    }

    @media screen and (max-width: 700px) {
        .sidebar a {
            float: revert-layer !important;  
        }
    }
    @media (min-width: 576px) {
    .col-sm-9 {
        -ms-flex: 0 0 75%;
        flex: 0 0 75%;
        max-width: 100% !important;
    }
}
 .btn-danger {
    margin-right: 15px !important;
}
.btn-info {
    margin-left: 10px !important;
}
h3{
    margin-left: 15px;
}

/* Ensure the search form is aligned to the right */
.search-form-container {
    display: flex;
    justify-content: flex-end; /* Push to the right */
    margin-bottom: 20px; /* Add spacing below */
}

/* Add styling for the search input and button */
.search-form-container input[type="text"] {
    width: 300px; /* Adjust width */
    border-radius: 5px;
    border: 1px solid #ddd;
    padding: 8px 12px;
    margin-right: 10px;
    font-size: 14px;
}

.search-form-container button {
    padding: 8px 15px;
    font-size: 14px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
}

.search-form-container button:hover {
    background-color: #0056b3;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .search-form-container {
        flex-direction: column; /* Stack vertically on smaller screens */
        align-items: flex-start;
    }

    .search-form-container input[type="text"] {
        margin-right: 0; /* Reset margin for stacking */
        margin-bottom: 10px;
        width: 100%; /* Use full width */
    }

    .search-form-container button {
        width: 100%; /* Use full width */
    }
}
</style>

<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>
   
    <div class="main-content"> <br><br>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Book Information</h3>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <?php
                // Get unique rental IDs
                $unique_rental_ids = array();
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($rental_row = mysqli_fetch_assoc($result)) {
                        // Only add rental ID if it's not already in the array
                        if (!in_array($rental_row['bhouse_id'], $unique_rental_ids)) {
                            $unique_rental_ids[] = $rental_row['bhouse_id'];
                            echo '<a href="add_walkin.php?rental_id=' . $rental_row['bhouse_id'] . '" class="btn btn-success me-2">Add Walk-In</a>';
                        }
                    }
                    // Reset the result pointer for the main data display
                    mysqli_data_seek($result, 0);
                }
                ?>
            </div>
            <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="d-flex">
                <input type="text" name="search_query" class="form-control me-2" placeholder="Search by firstname" value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <!-- Responsive Table -->
        <div class="table-responsive d-none d-md-block">  
              
                
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Firstname</th>
                        <th>Middlename</th>
                        <th>Lastname</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Date Started</th>
                        <th>Balance</th>
                        <th>Paid Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                     <?php
                    // Handle search query
        $search_query = isset($_GET['search_query']) ? '%' . $_GET['search_query'] . '%' : null;

        $query = "
            SELECT b.id, b.firstname, b.middlename, b.lastname, b.email, 
                   b.age, b.gender, b.contact_number, b.Address, 
                   b.date_posted, b.paid_amount, b.bhouse_id
            FROM book b
            INNER JOIN rental r ON b.bhouse_id = r.rental_id
            WHERE b.status = 'Confirm' 
            AND r.register1_id = ?
            " . ($search_query ? "AND b.firstname LIKE ?" : "") . "
            LIMIT ?, ?";

        $stmt = $dbconnection->prepare($query);

        if ($search_query) {
            $stmt->bind_param("isii", $current_user_id, $search_query, $offset, $results_per_page);
        } else {
            $stmt->bind_param("iii", $current_user_id, $offset, $results_per_page);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        // Reset URL if search_query is present
        if (isset($_GET['search_query'])) {
            echo '<script>
                window.history.replaceState({}, document.title, "' . htmlspecialchars($_SERVER['PHP_SELF']) . '");
            </script>';
        }
                    while ($row = mysqli_fetch_assoc($result)) {
                        $monthly_rental = getMonthlyRateForRental($row['bhouse_id']); 
                        $balance = calculateBalance($row['id'], $monthly_rental, $row['paid_amount']); 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($row['middlename']); ?></td>
                        <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['Address']); ?></td>
                        <td><?php echo date('F d, Y', strtotime($row['date_posted'])); ?></td>
                        <td><?php echo htmlspecialchars(number_format($balance, 2)); ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="paid_amount" min="0" step="0.01" placeholder="Enter Amount" required>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Card layout for mobile view -->
<div class="d-md-none">
    <?php
    mysqli_data_seek($result, 0); // Reset result pointer for mobile view
    while ($row = mysqli_fetch_assoc($result)) {
        $monthly_rental = getMonthlyRateForRental($row['bhouse_id']);
        $balance = calculateBalance($row['id'], $monthly_rental, $row['paid_amount']);
    ?>
    <div class="card">
        <div class="card-header">
            <h5><?php echo htmlspecialchars($row['firstname']) . ' ' . htmlspecialchars($row['lastname']); ?></h5>
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($row['age']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($row['gender']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($row['contact_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($row['Address']); ?></p>
            <p><strong>Date Started:</strong> <?php echo date('F d, Y', strtotime($row['date_posted'])); ?></p>
            <p><strong>Balance:</strong> <?php echo htmlspecialchars(number_format($balance, 2)); ?></p>

            <!-- Form to submit paid amount and delete record, side by side -->
            <div class="d-flex justify-content-between mt-2">
                <form method="post" action="">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="number" name="paid_amount" min="0" step="0.01" placeholder="Enter Amount" required>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <form method="post" action="">
                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>
</div><br>

        <div class="pagination">
            <a href="?pageno=1" class="btn btn-info">First</a>
            <a href="?pageno=<?php echo max(1, $pageno - 1); ?>" class="btn btn-info">Previous</a>
            <span>Page <?php echo $pageno; ?> of <?php echo $total_pages; ?></span>
            <a href="?pageno=<?php echo min($total_pages, $pageno + 1); ?>" class="btn btn-info">Next</a>
            <a href="?pageno=<?php echo $total_pages; ?>" class="btn btn-info">Last</a>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<?php
// Close the prepared statement and database connection
$stmt->close();
$dbconnection->close();
?>
