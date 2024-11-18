<?php
session_start(); // Start session at the beginning of the script

// Check if the admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit; // Stop further execution
}

// Access the first name from the session
$firstname = isset($_SESSION['firstname']) ? $_SESSION['firstname'] : 'Admin';

// Check if the user just logged in to show the welcome message
$showWelcomeMessage = false;
if (isset($_SESSION['just_loggedin']) && $_SESSION['just_loggedin']) {
    $showWelcomeMessage = true;
    unset($_SESSION['just_loggedin']); // Unset the variable to prevent the message from showing again
}

// Include header.php which contains necessary HTML and PHP code
include('header.php');
?>
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
</style>
 
   <?php if ($showWelcomeMessage): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "info",
                    title: "Welcome back, <?php echo htmlspecialchars($firstname); ?>!",
                    text: "HAVE A GOOD DAY!",
                    confirmButtonText: 'Thank you'
                });
            });
        </script>
    <?php endif; ?>


    <?php include('footer.php'); ?>
 

 
<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>

       <div class="main-content">
        <h3>Dashboard</h3>
      
       
        
       <div class="dashboard-cards">
            <!-- Boarding House Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">
                    <?php
                    $result = mysqli_query($dbconnection, "SELECT count(1) FROM rental");
                    $row = mysqli_fetch_array($result);
                    $total_boarding_houses = $row[0];
                    echo $total_boarding_houses;
                    ?>
                </div>
                <div class="font-14 text-secondary weight-500">
                    Boarding House
                </div>
            </div>
            <div class="widget-icon">
                <div class="icon" data-color="#00eccf">
                    <i class="fa fa-home animated-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

           <div class="dashboard-cards">
            <!-- Boarding House Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">
                    <?php
                    // Count the number of active subscriptions
                    $result = mysqli_query($dbconnection, "
                        SELECT COUNT(DISTINCT register1_id) 
                        FROM subscriptions 
                        WHERE status = 'active'
                    ");
                    $row = mysqli_fetch_array($result);
                    $total_active_subscriptions = $row[0];
                    echo $total_active_subscriptions;
                    ?>
                </div>
                <div class="font-14 text-secondary weight-500">
                    Number Landlords
                </div>
            </div>
            <div class="widget-icon">
                <div class="icon" data-color="#00eccf">
                    <i class="fa fa-thumbs-o-up animated-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <h3>Boarding House Ratings</h3>
    <canvas id="ratingChart"></canvas>
</div>
<div class="container">
    <h3>Boarding House Ratings</h3>
    <canvas id="monthlyBookingsChart"></canvas>
</div>
             
        </div>
        <br />
        <br />
        <br/>
<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


 
<?php

// Fetch average ratings for each boarding house
$ratingQuery = "
    SELECT r.title as rental_name, IFNULL(ROUND(AVG(NULLIF(b.ratings, 0)), 2), 0) as average_rating
    FROM rental r
    LEFT JOIN book b ON r.rental_id = b.bhouse_id
    GROUP BY r.title
";
$ratingResult = mysqli_query($dbconnection, $ratingQuery);

// Initialize arrays to store names and ratings for the chart
$rentalNames = [];
$rentalRatings = [];

if ($ratingResult) {
    while ($row = mysqli_fetch_assoc($ratingResult)) {
        $rentalNames[] = $row['rental_name'];
        $rentalRatings[] = $row['average_rating'];
    }
} else {
    echo "Error: " . mysqli_error($dbconnection);
}
// Fetch count of brokers for each boarding house
 


// Initialize an array for all months with zero bookings
$allMonths = [
    'January' => 0, 'February' => 0, 'March' => 0, 'April' => 0,
    'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0,
    'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0
];

// Fetch the number of bookings for each month
$monthlyBookingsQuery = "
    SELECT DATE_FORMAT(date_posted, '%Y-%m') as month, COUNT(id) as bookings
    FROM book
    GROUP BY month
    ORDER BY month ASC
";
$monthlyBookingsResult = mysqli_query($dbconnection, $monthlyBookingsQuery);

if ($monthlyBookingsResult) {
    while ($row = mysqli_fetch_assoc($monthlyBookingsResult)) {
        $date = DateTime::createFromFormat('Y-m', $row['month']);
        $monthName = $date->format('F'); // Get full month name
        $allMonths[$monthName] = $row['bookings'];
    }
} else {
    echo "Error: " . mysqli_error($dbconnection);
}

$months = array_keys($allMonths);
$bookings = array_values($allMonths);
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    var ctxRating = document.getElementById('ratingChart').getContext('2d');
    var ratingChart = new Chart(ctxRating, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($rentalNames); ?>,
            datasets: [{
                label: 'Average Rating',
                data: <?php echo json_encode($rentalRatings); ?>,
                backgroundColor: 'rgba(64, 191, 64, 0.6)',
                borderColor: 'rgba(64, 191, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5, // Set max to 5 for ratings
                    title: {
                        display: true,
                        text: 'Rating (1 to 5)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Boarding Houses'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
 
        var ctxBookings = document.getElementById('monthlyBookingsChart').getContext('2d');
        var monthlyBookingsChart = new Chart(ctxBookings, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Monthly Bookings',
                    data: <?php echo json_encode($bookings); ?>,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>



    </div>
</div>

<?php include('footer.php'); ?>
