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
    text-color: black;
}

/* Card box styles */
.card-box {
    background-color: #ffffff;
    border: 1px solid #e3e6f0;
    border-radius: 5px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    padding: 20px;
    margin-bottom: 20px;
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
    color: black !important;
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
    height: 400px; /* Adjust the height as needed */
    margin-top: 150px;
    margin-right: 300px;
}
</style>
 <body>
    <?php if ($showWelcomeMessage): ?>
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

      <div class="col-sm-9"> <br />
        <br />
        <h3>Dashboard</h3>
       
        
        <div class="row pb-10">
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
                                <i class="fa fa-home"></i>
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
                            <div class="font-14 text-secondary weight-500">
                                Requesting for Approval
                            </div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" data-color="#00eccf">
                                <i class="fa fa-envelope"></i>
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
                                <i class="fa fa-thumbs-o-up"></i>
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
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <br />

        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="incomeChart" style="margin-top: -40px;"></canvas>
                </div>
            </div>
            <div class="col-md-6">
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
            <canvas id="monthlyBookingsChart" style="margin-buttom: 225px;"></canvas>
        </div>
    </div>
</div>

        <?php
        $year = date('Y'); // Get the current year

$incomeQuery = "
    SELECT r.title as rental_name, IFNULL(COUNT(b.id) * r.monthly, 0) as total_income
    FROM rental r
    LEFT JOIN book b ON r.rental_id = b.bhouse_id AND b.status = 'Approved' AND YEAR(b.date_posted) = '$year'
    GROUP BY r.title
";
$incomeResult = mysqli_query($dbconnection, $incomeQuery);
$rentalNames = [];
$totalIncomes = [];

if ($incomeResult) {
    while ($row = mysqli_fetch_assoc($incomeResult)) {
        $rentalNames[] = $row['rental_name'];
        $totalIncomes[] = $row['total_income'];
    }
} else {
    echo "Error: " . mysqli_error($dbconnection);
}


        // Fetch ratings for each boarding house and include only those with ratings greater than 0
        $ratingQuery = "
            SELECT r.title as rental_name, IFNULL(ROUND(AVG(NULLIF(b.ratings, 0)), 2), 0) as average_rating
            FROM rental r
            LEFT JOIN book b ON r.rental_id = b.bhouse_id
            GROUP BY r.title
        ";
        $ratingResult = mysqli_query($dbconnection, $ratingQuery);
        $rentalRatings = [];

        if ($ratingResult) {
            while ($row = mysqli_fetch_assoc($ratingResult)) {
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

$year = date('Y'); // Get the current year

// Fetch the number of bookings for each month of the current year
$monthlyBookingsQuery = "
    SELECT DATE_FORMAT(date_posted, '%Y-%m') as month, COUNT(id) as bookings
    FROM book
    WHERE YEAR(date_posted) = '$year'
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
                var ctxIncome = document.getElementById('incomeChart').getContext('2d');
                var incomeChart = new Chart(ctxIncome, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($rentalNames); ?>,
                        datasets: [{
                            label: 'Boarding House Monthly Income (This Year)',
                            data: <?php echo json_encode($totalIncomes); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
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

                var ctxRating = document.getElementById('ratingChart').getContext('2d');
                var ratingChart = new Chart(ctxRating, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($rentalNames); ?>,
                        datasets: [{
                            label: 'Boarding House Ratings',
                            data: <?php echo json_encode($rentalRatings); ?>,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
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
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_keys($brokerPercentages)); ?>,
        datasets: [{
            label: 'Broker Distribution',
            data: <?php echo json_encode(array_values($brokerPercentages)); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'left', // Align legend to the left side
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2) + '%';
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
                label: 'Monthly Bookings (This Year)',
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
