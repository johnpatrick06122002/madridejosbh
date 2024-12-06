<?php include('header.php'); ?>
<?php 



// Check if the session exists
if (!isset($_SESSION['login_user'], $_SESSION['session_token'])) {
    header("Location: ../login.php");
    exit;
}

// Validate session token in the database
$login_user = $_SESSION['login_user'];
$session_token = $_SESSION['session_token'];

$stmt = $dbconnection->prepare("SELECT session_token FROM register1 WHERE email = ?");
$stmt->bind_param("s", $login_user);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row || $row['session_token'] !== $session_token) {
    // Session is invalid or logged out
    session_destroy();
    header("Location: ../login.php");
    exit;
}
$query = "
    SELECT r.title AS boarding_house, 
           MONTH(b.last_payment_date) AS month, 
           IFNULL(SUM(b.paid_amount), 0) AS monthly_income
    FROM rental r
    LEFT JOIN book b ON r.rental_id = b.bhouse_id 
        AND b.status = 'Confirm'
        AND YEAR(b.last_payment_date) = YEAR(CURRENT_DATE)
    WHERE r.register1_id = '$login_session'
    GROUP BY r.title, MONTH(b.last_payment_date)
    ORDER BY r.title, MONTH(b.last_payment_date)
";

$result = mysqli_query($dbconnection, $query);

$boarding_houses = [];
$monthly_incomes = [];
$monthly_data = []; // Array to store month-wise income
$total_income = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $boarding_houses[] = $row['boarding_house'];
        $monthly_incomes[] = $row['monthly_income'];

        // Store income by month for each boarding house
        $month_name = date('F', mktime(0, 0, 0, $row['month'], 1)); // Convert month number to name
        $monthly_data[$row['boarding_house']][$month_name] = $row['monthly_income'];
        
        $total_income += $row['monthly_income'];  // Accumulate total income
    }
} else {
    echo "Error fetching data: " . mysqli_error($dbconnection);
}


// Query to fetch brokers count for each boarding house
$brokers_query = "
    SELECT r.title as boarding_house, COUNT(b.id) as broker_count
    FROM rental r
    JOIN book b ON r.rental_id = b.bhouse_id
    WHERE r.id = '$login_session' AND b.status = 'Confirm'
    GROUP BY r.title
";

$brokers_result = mysqli_query($dbconnection, $brokers_query);

$brokers_data = [];
$total_brokers = 0;

if ($brokers_result) {
    while ($row = mysqli_fetch_assoc($brokers_result)) {
        $brokers_data[] = $row;
        $total_brokers += $row['broker_count'];
    }
} else {
    echo "Error fetching data: " . mysqli_error($dbconnection);
}

$broker_labels = array_column($brokers_data, 'boarding_house');
$broker_counts = array_column($brokers_data, 'broker_count');
$broker_percentages = [];

foreach ($broker_counts as $count) {
    $broker_percentages[] = ($count / $total_brokers) * 100;
}

$monthly_total_income = array_fill(1, 12, 0); // Initialize all months from January (1) to December (12) with 0

$monthly_income_query = "
    SELECT MONTH(b.date_posted) as month, IFNULL(SUM(r.monthly), 0) as total_income
    FROM rental r
    LEFT JOIN book b ON r.rental_id = b.bhouse_id AND b.status = 'Confirm'
    WHERE r.id  = ?
    GROUP BY MONTH(b.date_posted)
";

if ($stmt = mysqli_prepare($dbconnection, $monthly_income_query)) {
    mysqli_stmt_bind_param($stmt, "i", $login_session); // Assuming $login_session is an integer
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $month, $total_income);

    while (mysqli_stmt_fetch($stmt)) {
        $monthly_total_income[$month] = $total_income;
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing the query: " . mysqli_error($dbconnection);
}

// Query to fetch total monthly income across all boarding houses for the current landlord
$query = "
    SELECT r.title AS boarding_house, 
           MONTH(b.last_payment_date) AS month, 
           IFNULL(SUM(b.paid_amount), 0) AS monthly_income
    FROM rental r
    LEFT JOIN book b ON r.rental_id = b.bhouse_id 
        AND b.status = 'Confirm'
        AND YEAR(b.last_payment_date) = YEAR(CURRENT_DATE)
    WHERE r.register1_id = '$login_session'
    GROUP BY r.title, MONTH(b.last_payment_date)
    ORDER BY r.title, MONTH(b.last_payment_date)
";

$result = mysqli_query($dbconnection, $query);

$boarding_houses = [];
$monthly_incomes = [];
$monthly_data = []; // Array to store month-wise income
$total_income = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $boarding_houses[] = $row['boarding_house'];
        $monthly_incomes[] = $row['monthly_income'];

        // Store income by month for each boarding house
        $month_name = date('F', mktime(0, 0, 0, $row['month'], 1)); // Convert month number to name
        $monthly_data[$row['boarding_house']][$month_name] = $row['monthly_income'];
        
        $total_income += $row['monthly_income'];  // Accumulate total income
    }
} else {
    echo "Error fetching data: " . mysqli_error($dbconnection);
}

 

// Initialize array for monthly bookings
$monthly_bookings = array_fill(1, 12, 0); // January (1) to December (12)

// SQL query for total number of bookings per month
$monthly_bookings_query = "
    SELECT MONTH(date_posted) AS month, COUNT(*) AS total_bookings
    FROM book
    WHERE YEAR(date_posted) = YEAR(CURDATE())
    AND register1_id = ?
    GROUP BY MONTH(date_posted)
    ORDER BY MONTH(date_posted)
";

if ($stmt = mysqli_prepare($dbconnection, $monthly_bookings_query)) {
    // Bind parameters
    mysqli_stmt_bind_param($stmt, "i", $login_session); // Assuming $login_session is an integer

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $month, $total_bookings);

        // Fetch results
        while (mysqli_stmt_fetch($stmt)) {
            if ($month >= 1 && $month <= 12) { // Ensure valid month
                $monthly_bookings[$month] = $total_bookings;
            } else {
                error_log("Invalid month returned: $month"); // Debug invalid month
            }
        }
    } else {
        // Log execution error
        error_log("Query execution failed: " . mysqli_error($dbconnection));
    }

    mysqli_stmt_close($stmt);
} else {
    // Log query preparation error
    error_log("Error preparing query: " . mysqli_error($dbconnection));
}

 
?>
<!-- Modified HTML structure -->
<style>
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
    background: #f4f6f9;
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
    background: linear-gradient(28deg, #bed3e3, #b9cbd1);
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 20px;
    color: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

/* Card content */
.widget-style3 {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-data {
    flex-grow: 1;
}

.widget-icon {
    font-size: 36px;
    color: black;
}

.font-24 {
    font-size: 24px;
    font-weight: bold;
    color:black;
}

.font-14 {
    font-size: 14px;
    color:black;
}

/* Chart containers */
.chart-container1, .chart-container2 {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
}
</style>

<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>
    
    <div class="main-content">
        <h3>Dashboard</h3>
        
        <div class="dashboard-cards">
            <!-- Boarding House Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="font-24">
                            <?php
                            $result = mysqli_query($dbconnection, "SELECT count(1) FROM rental WHERE register1_id ='$login_session'");
                            echo $result ? mysqli_fetch_array($result)[0] : 0;
                            ?>
                        </div>
                        <div class="font-14">Boarding House</div>
                    </div>
                    <div class="widget-icon">
                        <i class="fa fa-home"></i>
                    </div>
                </div>
            </div>

            <!-- Requesting Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="font-24">
                            <?php
                            $result = mysqli_query($dbconnection, "SELECT count(*) FROM book WHERE register1_id='$login_session' AND status=''");
                            echo $result ? mysqli_fetch_array($result)[0] : 0;
                            ?>
                        </div>
                        <div class="font-14">Requesting</div>
                    </div>
                    <div class="widget-icon">
                        <i class="fa fa-envelope"></i>
                    </div>
                </div>
            </div>

            <!-- Confirmed Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="font-24">
                            <?php
                            $result = mysqli_query($dbconnection, "SELECT COUNT(*) FROM book WHERE register1_id='$login_session' AND status='Confirm'");
                            echo $result ? mysqli_fetch_array($result)[0] : 0;
                            ?>
                        </div>
                        <div class="font-14">Confirmed</div>
                    </div>
                    <div class="widget-icon">
                        <i class="fa fa-thumbs-o-up"></i>
                    </div>
                </div>
            </div>

            <!-- Total Monthly Income Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="font-24">
                            <?php echo number_format($total_income); ?>
                        </div>
                        <div class="font-14">Total Monthly Income</div>
                    </div>
                    <div class="widget-icon">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-container1">
            <h3>Boarding House Monthly Income</h3>
            <canvas id="monthlyIncomeChart"></canvas>
        </div>

        <div class="chart-container2">
            <h3>Boarding House Ratings</h3>
            <canvas id="monthlyBookingsChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Chart for Monthly Income
    var ctxIncome = document.getElementById('monthlyIncomeChart').getContext('2d');
    var monthlyIncomeChart = new Chart(ctxIncome, {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [
                <?php foreach ($monthly_data as $house => $months) { ?>
                {
                    label: '<?php echo $house; ?>',
                                           data: [
                            <?php 
                            for ($i = 1; $i <= 12; $i++) {
                                $month_name = date('F', mktime(0, 0, 0, $i, 1));
                                echo isset($months[$month_name]) ? $months[$month_name] : 0;
                                echo ($i < 12) ? ',' : '';  // Add a comma after each value except the last
                            }
                            ?>
                        ],
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                <?php } ?>
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart for Boarding House Ratings
   var ctxBookings = document.getElementById('monthlyBookingsChart').getContext('2d');

// Create a linear gradient for the line chart
var gradientLine = ctxBookings.createLinearGradient(0, 0, 0, 400);
gradientLine.addColorStop(0, 'rgba(75, 192, 192, 1)'); // Top color (light blue)
gradientLine.addColorStop(1, 'rgba(75, 192, 192, 0.2)'); // Bottom color (lighter blue)

var monthlyBookingsChart = new Chart(ctxBookings, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Bookings',
            data: <?php echo json_encode(array_values($monthly_bookings)); ?>,
            fill: true, // Fill the area under the line
            backgroundColor: gradientLine, // Apply the gradient background
            borderColor: 'rgba(75, 192, 192, 1)', // Line color
            pointBackgroundColor: 'rgba(75, 192, 192, 1)', // Points color
            pointHoverBackgroundColor: 'rgba(75, 192, 192, 1)', // Hover point color
            pointBorderColor: '#fff', // Point border color
            pointHoverBorderColor: '#fff', // Hover point border color
            tension: 0.4 // Smooth curve of the line
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
});
</script>
