<?php
session_start(); // Start session at the beginning of the script

// Check if the admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit; // Stop further execution
}

// Check if the user just logged in to show the welcome message
$showWelcomeMessage = false;
if (isset($_SESSION['just_loggedin']) && $_SESSION['just_loggedin']) {
    $showWelcomeMessage = true;
    unset($_SESSION['just_loggedin']); // Unset the variable to prevent the message from showing again
}

include('header.php'); // Include header.php which contains necessary HTML and PHP code
?>
<style>  
/* Container styles */
.row.pb-10 {
    padding-bottom: 10px;
    
}
.text-secondary {
    color: black !important;
}
/* Card box styles */
.card-box {
    background-color: #ffffff;
    border: 1px solid #e3e6f0;
    border-radius: 5px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    padding: 20px;
    margin-bottom: 20px;
    text-color: black;
}

.card-box.height-100-p {
    height: 100%;
}

/* Widget styles */
.widget-style3 {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-data {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.weight-700 {
    font-weight: 700;
}

.font-24 {
    font-size: 24px;
}

.text-dark {
    color: #5a5c69;
}

.font-14 {
    font-size: 14px;
}

.text-secondary {
    color: #858796;
}

.weight-500 {
    font-weight: 500;
}

/* Widget icon styles */
.widget-icon {
    display: flex;
    align-items: center;
}

.widget-icon .icon {
    font-size: 2em;
    color: #00eccf;
}

/* Custom width for .col-xl-3 on screens that are at least 1200px wide */
@media (min-width: 1200px) {
    .col-xl-3 {
        -ms-flex: 0 0 25%;
        flex: 0 0 25%;
        max-width: 25%;
    }
}
.fa {
    display: inline-block;
    font: normal normal normal 14px / 1 FontAwesome;
    font-size: inherit;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    float: right; /* or */
    text-align: right; /* or */
    margin-left: 80px; /* or */
    color: black;

    /* any other method to position right */
}
.chart-container {
    width: 100%;
    height: auto;
}

.chart-container3 {
    position: relative;
    width: 100%;  /* Adjust the width as needed */
    height: 450px; /* Adjust the height as needed */
    margin-top: 210px;
    margin-right: 300px;
}
@keyframes pulse {
    0% {
        transform: scale(1.5);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.animated-icon {
    animation: pulse 1.3s infinite;
}

</style>
 <body>
    <?php if ($showWelcomeMessage): ?>
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "info",
                title: "Welcome back, Admin!",
                text: "HAVE A GOOD DAY!",
                confirmButtonText: 'Thank you'
            });
        });
    </script>
    <?php endif; ?>

    <?php include('footer.php'); ?>
</body>
<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>

      <div class="col-sm-9">  <br /> <br />
        <h3>Dashboard</h3>
      
       
        
        <div class="row pb-10" >
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
    <div class="card-box height-100-p widget-style3">
        <div class="d-flex flex-wrap">
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

            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark">
                                <?php
                                $result = mysqli_query($dbconnection, "SELECT count(1) FROM landlords WHERE status=''");
                                $row = mysqli_fetch_array($result);
                                $total_requests = $row[0];
                                echo $total_requests;
                                ?>
                            </div>
                            <div class="font-14 text-secondary weight-500 ">
                                Requesting for Approval
                            </div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" data-color="#00eccf">
                                <i class="fa fa-envelope animated-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark">
                                <?php
                                $result = mysqli_query($dbconnection, "SELECT count(1) FROM landlords WHERE status='Approved'");
                                $row = mysqli_fetch_array($result);
                                $total_approved_owners = $row[0];
                                echo $total_approved_owners;
                                ?>
                            </div>
                            <div class="font-14 text-secondary weight-500">
                                Approved Owners
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
            
             <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark">
                                <?php
                                $query = "
                                    SELECT SUM(r.monthly) as total_income
                                    FROM rental r
                                    JOIN book b ON r.rental_id = b.bhouse_id
                                    WHERE b.status = 'Approved'
                                ";
                                $resultincome = mysqli_query($dbconnection, $query);
                                if ($resultincome) {
                                    $row = mysqli_fetch_array($resultincome);
                                    $total_income = $row['total_income'];
                                    echo number_format($total_income);
                                } else {
                                    echo "Error: " . mysqli_error($dbconnection);
                                }
                                ?>
                            </div>
                            <div class="font-14 text-secondary weight-500">
                                Total Monthly Income
                            </div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" data-color="#00eccf">
                                <i class="fa fa-money animated-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <br />
        <br/>
<div class="row">
    <div class="col-md-12">
        <div class="chart-container">
            <canvas id="ratingChart" style="margin-top: -40px;"></canvas>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="chart-container">
            <canvas id="brokerPieChart" style="margin-top: -40px; width: 330px; height: 330px;"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container3">
            <canvas id="monthlyBookingsChart" style="margin-bottom: 225px;"></canvas>
        </div>
    </div>
</div>

<?php

// Removed the income query and PHP code related to fetching and processing income data

// Fetch ratings for each boarding house and include only those with ratings greater than 0
$ratingQuery = "
    SELECT r.title as rental_name, IFNULL(ROUND(AVG(NULLIF(b.ratings, 0)), 2), 0) as average_rating
    FROM rental r
    LEFT JOIN book b ON r.rental_id = b.bhouse_id
    GROUP BY r.title
";
$ratingResult = mysqli_query($dbconnection, $ratingQuery);
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
$brokerQuery = "
    SELECT r.title as rental_name, COUNT(b.id) as broker_count
    FROM rental r
    LEFT JOIN book b ON r.rental_id = b.bhouse_id AND b.status = 'Approved'
    GROUP BY r.title
";
$brokerResult = mysqli_query($dbconnection, $brokerQuery);
$rentalBrokers = [];
$totalBrokers = 0;

if ($brokerResult) {
    while ($row = mysqli_fetch_assoc($brokerResult)) {
        $rentalBrokers[$row['rental_name']] = $row['broker_count'];
        $totalBrokers += $row['broker_count'];
    }
} else {
    echo "Error: " . mysqli_error($dbconnection);
}

// Calculate percentages for brokers
$brokerPercentages = [];
foreach ($rentalBrokers as $rental => $brokers) {
    $percentage = ($brokers / $totalBrokers) * 100;
    $brokerPercentages[$rental] = round($percentage, 2);
}

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
                    label: 'Boarding House Ratings',
                    data: <?php echo json_encode($rentalRatings); ?>,
                    backgroundColor: '#40bf40',
                    borderColor: '#40bf40', 
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5 // Set max to 5 for ratings
                    }
                }
            }
        });

        var ctxBroker = document.getElementById('brokerPieChart').getContext('2d');
        var brokerPieChart = new Chart(ctxBroker, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($brokerPercentages)); ?>,
                datasets: [{
                    label: 'Broker Distribution',
                    data: <?php echo json_encode(array_values($brokerPercentages)); ?>,
                    backgroundColor: [
                        '#40bf40',
                        '#ff1a1a',
                        '#ff00ff',
                        '#000066',
                        '#006600',
                        '#33cc33',
                        '#ffbf80',
                        '#ffff00',
                        '#ff4da6',
                        '#805500',
                        '#adad85',
                        '#6699ff',
                        '#00ffff',
                        '#ff9966',
                        '#1f1f14',
                        '#331a33',
                        '#00ffcc',
                        '#b30000'
                    ],
                    borderColor: '#ffffff', // Border color of the slices
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                cutout: '65%', // Adjust the size of the hole here (e.g., 65%)
                plugins: {
                    legend: {
                        position: 'left',
                        align: 'center', // Align legend items to the center
                        labels: {
                            padding: 20, // Add padding around legend items
                            font: {
                                size: 14 // Adjust font size of legend items
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: ' ',
                        padding: {
                            top: 20, // Adjust top padding
                            bottom: 20 // Adjust bottom padding
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.raw.toFixed(2) + '%';
                                return label;
                            }
                        }
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
