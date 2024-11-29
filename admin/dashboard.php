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
    background: linear-gradient(145deg, #ffffff, #f4f4f4);
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

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

.widget-icon .icon {
    font-size: 36px;
    color: #00bccf;
}

.font-24 {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.font-14 {
    font-size: 14px;
    color: #6c757d;
}

h3 {
    color: #333;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Chart containers */
.chart-container1, .chart-container2, .chart-container3 {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

canvas {
    max-width: 100%;
    height: auto !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dashboard-container {
        flex-direction: column;
    }
    
    .sidebar-container {
        width: 100%;
        position: static;
    }
    
    .main-content {
        padding: 15px;
    }
    
    .card-box {
        min-width: 100%;
    }
    
    canvas {
        height: 300px !important;
    }
}

/* SweetAlert2 Custom Styling */
.swal2-popup {
    font-size: 16px !important;
    border-radius: 10px;
}

</style>

<!-- Welcome Message -->
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
                            $result = mysqli_query($dbconnection, "SELECT count(1) FROM rental");
                            $row = mysqli_fetch_array($result);
                            $total_boarding_houses = $row[0];
                            echo $total_boarding_houses;
                            ?>
                        </div>
                        <div class="font-14">Boarding House</div>
                    </div>
                    <div class="widget-icon">
                        <div class="icon">
                            <i class="fa fa-home"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Landlords Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="font-24">
                            <?php
                            
                            $result = mysqli_query($dbconnection, "
                              SELECT COUNT(DISTINCT id) AS total_landlords
                    FROM register1 
                    WHERE confirmation = 'approved'
                            ");
                            $row = mysqli_fetch_array($result);
                            $total_active_subscriptions = $row[0];
                            echo $total_active_subscriptions;
                            ?>
                        </div>
                        <div class="font-14">Number of Landlords</div>
                    </div>
                    <div class="widget-icon">
                        <div class="icon">
                            <i class="fa fa-thumbs-o-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="chart-container1">
            <h3>Boarding House Ratings</h3>
            <canvas id="ratingChart"></canvas>
        </div>
        <div class="chart-container2">
            <h3>Monthly Bookings</h3>
            <canvas id="monthlyBookingsChart"></canvas>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
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
    // Boarding House Ratings Chart
    var ctxRating = document.getElementById('ratingChart').getContext('2d');
    var gradientBar = ctxRating.createLinearGradient(0, 0, 0, 400);
    gradientBar.addColorStop(0, 'rgba(54, 162, 235, 1)'); // Top color
    gradientBar.addColorStop(1, 'rgba(75, 192, 192, 0.8)'); // Bottom color

    var ratingChart = new Chart(ctxRating, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($rentalNames); ?>,
            datasets: [{
                label: 'Average Rating',
                data: <?php echo json_encode($rentalRatings); ?>,
                backgroundColor: gradientBar, // Gradient fill
                borderColor: 'rgba(54, 162, 235, 1)', // Border color
                borderWidth: 2,
                hoverBackgroundColor: 'rgba(54, 162, 235, 0.8)' // Hover effect
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#333'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    title: {
                        display: true,
                        text: 'Rating (1 to 5)',
                        color: '#333',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        color: '#666'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Boarding Houses',
                        color: '#333',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        color: '#666'
                    }
                }
            }
        }
    });

    // Monthly Bookings Chart
    var ctxBookings = document.getElementById('monthlyBookingsChart').getContext('2d');
    var gradientLine = ctxBookings.createLinearGradient(0, 0, 0, 400);
    gradientLine.addColorStop(0, 'rgba(255, 99, 132, 1)'); // Top color
    gradientLine.addColorStop(1, 'rgba(255, 159, 64, 0.8)'); // Bottom color

    var monthlyBookingsChart = new Chart(ctxBookings, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Monthly Bookings',
                data: <?php echo json_encode($bookings); ?>,
                fill: true,
                backgroundColor: gradientLine, // Gradient fill
                borderColor: 'rgba(255, 99, 132, 1)', // Line color
                pointBackgroundColor: 'rgba(255, 99, 132, 1)', // Points
                pointHoverBackgroundColor: 'rgba(255, 159, 64, 1)', // Hover points
                pointBorderColor: '#fff',
                pointHoverBorderColor: '#fff',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#333'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Bookings',
                        color: '#333',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        color: '#666'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Months',
                        color: '#333',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        color: '#666'
                    }
                }
            }
        }
    });
});
</script>


    </div>
</div>

<?php include('footer.php'); ?>
