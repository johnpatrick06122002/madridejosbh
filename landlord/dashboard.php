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
    // Destroy the session
    session_destroy();

    // Output SweetAlert and redirect
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Logged Out',
            text: 'You\'ve been logged out. Kindly login again.',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../login.php';
            } else {
                window.location.href = '../login.php';
            }
        });
    </script>";
    exit;
}

$query = "
    SELECT 
        r.title AS boarding_house, 
        MONTH(p.last_date_pay) AS month, 
        IFNULL(SUM(p.amount), 0) AS monthly_income
    FROM rental r
    LEFT JOIN payment p ON r.rental_id = p.rental_id
    LEFT JOIN booking b ON b.payment_id = p.payment_id 
        AND b.status = 'Confirm'
        AND YEAR(p.last_date_pay) = YEAR(CURRENT_DATE)
    WHERE r.register1_id = ?
    GROUP BY r.title, MONTH(p.last_date_pay)
    ORDER BY r.title, MONTH(p.last_date_pay);
";

$stmt = $dbconnection->prepare($query);
$stmt->bind_param("i", $login_session);
$stmt->execute();
$result = $stmt->get_result();

$boarding_houses = [];
$monthly_data = []; // Array to store month-wise income
$total_income = 0;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $boarding_house = $row['boarding_house'];
        $month_name = date('F', mktime(0, 0, 0, $row['month'], 1)); // Convert month number to name
        $monthly_income = $row['monthly_income'];

        // Store income data
        if (!isset($monthly_data[$boarding_house])) {
            $monthly_data[$boarding_house] = [];
        }
        $monthly_data[$boarding_house][$month_name] = $monthly_income;

        $total_income += $monthly_income; // Accumulate total income
    }
} else {
    echo "Error fetching data: " . mysqli_error($dbconnection);
}
  
 // Fetch Monthly Income Data
$monthly_income_query = "
    SELECT 
        r.title AS boarding_house, 
        MONTH(p.last_date_pay) AS month, 
        IFNULL(SUM(p.amount), 0) AS monthly_income
    FROM rental r
    LEFT JOIN payment p ON r.rental_id = p.rental_id
    LEFT JOIN booking b ON b.payment_id = p.payment_id 
        AND b.status = 'Confirm'
        AND YEAR(p.last_date_pay) = YEAR(CURRENT_DATE)
    WHERE r.register1_id = ?
    GROUP BY r.title, MONTH(p.last_date_pay)
    ORDER BY r.title, MONTH(p.last_date_pay);
";

$stmt = $dbconnection->prepare($monthly_income_query);
$stmt->bind_param("i", $login_session);
$stmt->execute();
$income_result = $stmt->get_result();

$income_data = [];
while ($row = $income_result->fetch_assoc()) {
    $month = date('F', mktime(0, 0, 0, $row['month'], 1)); // Convert month number to name
    $income_data[$month] = $row['monthly_income'];
}

// Fetch Monthly Booking Data
$monthly_booking_query = "
    SELECT 
        MONTH(b.date_posted) AS month, 
        COUNT(*) AS booking_count
    FROM booking b
    INNER JOIN payment p ON b.payment_id = p.payment_id
    INNER JOIN rental r ON p.rental_id = r.rental_id
    WHERE r.register1_id = ?
    AND YEAR(b.date_posted) = YEAR(CURRENT_DATE)
    GROUP BY MONTH(b.date_posted)
    ORDER BY MONTH(b.date_posted);
";

$stmt = $dbconnection->prepare($monthly_booking_query);
$stmt->bind_param("i", $login_session);
$stmt->execute();
$booking_result = $stmt->get_result();

$booking_data = [];
while ($row = $booking_result->fetch_assoc()) {
    $month = date('F', mktime(0, 0, 0, $row['month'], 1)); // Convert month number to name
    $booking_data[$month] = $row['booking_count'];
}

// Fetch boarders' names and ratings
$ratings_query = "
    SELECT 
        CONCAT(b.firstname, ' ', b.lastname) AS boarder_name, 
        b.ratings AS rating
    FROM booking b
    INNER JOIN payment p ON b.payment_id = p.payment_id
    INNER JOIN rental r ON p.rental_id = r.rental_id
    WHERE b.ratings > 0
    ORDER BY b.ratings DESC; -- Sort by ratings for better visualization
";

$result = $dbconnection->query($ratings_query);

if (!$result) {
    die("Query Failed: " . $dbconnection->error);
}

// Prepare data for the chart
$boarders = [];
$ratings = [];

while ($row = $result->fetch_assoc()) {
    $boarders[] = $row['boarder_name']; // Collect boarders' names
    $ratings[] = $row['rating'];       // Collect ratings
}

// Convert data to JSON for Chart.js
$boarders_json = json_encode($boarders);
$ratings_json = json_encode($ratings);
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

           <div class="card-box">
    <div class="widget-style3">
        <div class="widget-data">
            <div class="font-24">
                <?php
                $query = "
                    SELECT COUNT(*) 
                    FROM booking 
                    WHERE payment_id IN (
                        SELECT payment_id 
                        FROM payment 
                        WHERE rental_id IN (
                            SELECT rental_id 
                            FROM rental 
                            WHERE register1_id = '$login_session'
                        )
                    ) AND status = 'Pending'
                ";
                $result = mysqli_query($dbconnection, $query);
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
                $query = "
                    SELECT COUNT(*) 
                    FROM booking 
                    WHERE payment_id IN (
                        SELECT payment_id 
                        FROM payment 
                        WHERE rental_id IN (
                            SELECT rental_id 
                            FROM rental 
                            WHERE register1_id = '$login_session'
                        )
                    ) AND status = 'Confirm'
                ";
                $result = mysqli_query($dbconnection, $query);
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
                            <?php echo number_format($monthly_income); ?>
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
            <h3>Monthly Booking</h3>
            <canvas id="monthlyBookingsChart"></canvas>
        </div>
        <div class="chart-container3">
    <h3>Ratings Per Boarder</h3>
    <canvas id="boarderRatingsChart"></canvas>
</div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script>
// Data for the charts (populated from PHP)
const incomeData = <?php echo json_encode($income_data); ?>;
const bookingData = <?php echo json_encode($booking_data); ?>;

// Define all months of the year
const allMonths = [
    'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
    'September', 'October', 'November', 'December'
];

// Fill missing months with zero data for income and bookings
const incomeDataFilled = allMonths.map(month => incomeData[month] || 0);
const bookingDataFilled = allMonths.map(month => bookingData[month] || 0);

// Bar Graph: Monthly Income
const incomeChartCtx = document.getElementById('monthlyIncomeChart').getContext('2d');
new Chart(incomeChartCtx, {
    type: 'bar',
    data: {
        labels: allMonths,
        datasets: [{
            label: 'Monthly Income',
            data: incomeDataFilled,
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Income ($)' }
            },
            x: { title: { display: true, text: 'Months' } }
        }
    }
});

// Line Graph: Monthly Bookings
const bookingChartCtx = document.getElementById('monthlyBookingsChart').getContext('2d');

// Create a gradient for the line color
const gradient = bookingChartCtx.createLinearGradient(0, 0, 0, 400); // Vertical gradient
gradient.addColorStop(0, 'rgba(153, 102, 255, 1)'); // Start color (purple)
gradient.addColorStop(1, 'rgba(75, 192, 192, 1)'); // End color (light teal)

new Chart(bookingChartCtx, {
    type: 'line',
    data: {
        labels: allMonths,
        datasets: [{
            label: 'Monthly Bookings',
            data: bookingDataFilled,
            backgroundColor: gradient, // Use gradient for background
            borderColor: gradient, // Use gradient for border
            borderWidth: 2,
            fill: true, // Fill the area below the line with the gradient
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Bookings' }
            },
            x: { title: { display: true, text: 'Months' } }
        }
    }
});
// Ratings data from PHP
const boarders = <?php echo $boarders_json; ?>;
const ratings = <?php echo $ratings_json; ?>;

// Bar Chart: Ratings Per Boarder
const ratingsChartCtx = document.getElementById('boarderRatingsChart').getContext('2d');
new Chart(ratingsChartCtx, {
    type: 'bar',
    data: {
        labels: boarders, // Boarders' names as labels
        datasets: [{
            label: 'Ratings',
            data: ratings,
            backgroundColor: 'rgba(54, 162, 235, 0.5)', // Light blue
            borderColor: 'rgba(54, 162, 235, 1)',       // Dark blue
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 5, // Ratings range from 0 to 5
                title: { display: true, text: 'Ratings (0 to 5)' }
            },
            x: {
                title: { display: true, text: 'Boarders' },
                ticks: {
                    autoSkip: false, // Show all boarders
                    maxRotation: 90, // Rotate labels if they overlap
                    minRotation: 45  // Minimum rotation for better visibility
                }
            }
        }
    }
});
</script>