<?php include('header.php'); ?>
<?php 

// Initialize arrays
$boarding_houses = [];
$monthly_incomes = [];
$total_income = 0;

// Query to fetch all boarding houses and their monthly incomes for the current landlord
$query = "
    SELECT r.title as boarding_house, IFNULL(SUM(r.monthly), 0) as monthly_income
    FROM rental r
    LEFT JOIN book b ON r.rental_id = b.bhouse_id AND b.status = 'Approved'
    WHERE r.id = '$login_session'
    GROUP BY r.title
";

$result = mysqli_query($dbconnection, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $boarding_houses[] = $row['boarding_house'];
        $monthly_incomes[] = $row['monthly_income'];
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
    WHERE r.id = '$login_session' AND b.status = 'Approved'
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
    LEFT JOIN book b ON r.rental_id = b.bhouse_id AND b.status = 'Approved'
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
$total_income_query = "
    SELECT IFNULL(SUM(r.monthly), 0) as total_income
    FROM rental r
    LEFT JOIN book b ON r.rental_id = b.bhouse_id AND b.status = 'Approved'
    WHERE r.id  = '$login_session'
";

$total_income_result = mysqli_query($dbconnection, $total_income_query);

if ($total_income_result) {
    $row = mysqli_fetch_assoc($total_income_result);
    $total_income = $row['total_income']; // Use the total income sum
} else {
    echo "Error fetching total income: " . mysqli_error($dbconnection);
}

// Initialize array for monthly bookings
$monthly_bookings = array_fill(1, 12, 0); // Initialize all months from January (1) to December (12) with 0

// Prepare SQL query for total number of bookings per month
$monthly_bookings_query = "
    SELECT MONTH(date_posted) AS month, COUNT(*) AS total_bookings
    FROM book
    WHERE YEAR(date_posted) = YEAR(CURDATE())
    AND id  = ?
    GROUP BY MONTH(date_posted)
    ORDER BY MONTH(date_posted)
";

if ($stmt = mysqli_prepare($dbconnection, $monthly_bookings_query)) {
    mysqli_stmt_bind_param($stmt, "i", $login_session); // Assuming $login_session is an integer
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $month, $total_bookings);

    while (mysqli_stmt_fetch($stmt)) {
        $monthly_bookings[$month] = $total_bookings;
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing the query: " . mysqli_error($dbconnection);
}

?>

<style>  
/* Container styles */
.row.pb-10 {
    padding-bottom: 10px;
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
        max-width: 20%;
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
    margin-left: 70px; /* or */

    /* any other method to position right */
}
.chart-container1 {
    position: relative;
    width: 80%;  /* Adjust the width as needed */
    height: 450px; /* Adjust the height as needed */
}
.chart-container2 {
    position: relative;
    width: 70%;  /* Adjust the width as needed */
    height: 450px; /* Adjust the height as needed */
    margin-left: 1px;
}
.chart-container3 {
    
    width: 82%;  /* Adjust the width as needed */
    height: 420px;
     
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

<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="col-sm-10">
        <br />
        <h3>Dashboard</h3>
        <br />
        <br />
       <div class="row pb-10">
    <!-- Boarding House Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap align-items-center">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark">
                        <?php
                        $result = mysqli_query($dbconnection, "SELECT count(1) FROM rental WHERE register1_id ='$login_session'");
                        if ($result) {
                            $row = mysqli_fetch_array($result);
                            $total = $row[0];
                            echo $total;
                        } else {
                            echo "Error: " . mysqli_error($dbconnection);
                        }
                        ?>
                    </div>
                    <div class="font-14 text-secondary weight-500">
                        Boarding House
                    </div>
                </div>
                <div class="widget-icon ml-auto">
                    <div class="icon" data-color="#00eccf">
                        <i class="fa fa-home animated-icon" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Requesting Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap align-items-center">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark">
                        <?php
                        $result = mysqli_query($dbconnection, "SELECT count(1) FROM book WHERE landlord_id='$login_session' AND status=''");
                        if ($result) {
                            $row = mysqli_fetch_array($result);
                            $total = $row[0];
                            echo $total;
                        } else {
                            echo "Error: " . mysqli_error($dbconnection);
                        }
                        ?>
                    </div>
                    <div class="font-14 text-secondary weight-500">
                        Requesting
                    </div>
                </div>
                <div class="widget-icon ml-auto">
                    <div class="icon" data-color="#00eccf">
                        <i class="fa fa-envelope animated-icon" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approved Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap align-items-center">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark">
                        <?php
                        $result = mysqli_query($dbconnection, "SELECT count(1) FROM book WHERE landlord_id='$login_session' AND status='Approved'");
                        if ($result) {
                            $row = mysqli_fetch_array($result);
                            $total = $row[0];
                            echo $total;
                        } else {
                            echo "Error: " . mysqli_error($dbconnection);
                        }
                        ?>
                    </div>
                    <div class="font-14 text-secondary weight-500">
                        Approved
                    </div>
                </div>
                <div class="widget-icon ml-auto">
                    <div class="icon" data-color="#00eccf">
                        <i class="fa fa-thumbs-o-up animated-icon" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Monthly Income Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap align-items-center">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark">
                        <?php echo number_format($total_income); ?>
                    </div>
                    <div class="font-14 text-secondary weight-500">
                        Total Monthly Income
                    </div>
                </div>
                <div class="widget-icon ml-auto">
                    <div class="icon" data-color="#00eccf">
                        <i class="fa fa-money animated-icon" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <br />

        <div class="row">
    <div class="col-md-6">
        <!-- Bar Chart for Monthly Incomes of Boarding Houses -->
        <div class="chart-container1">
            <canvas id="monthlyIncomeChart"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <!-- Pie Chart for Brokers Percentage -->
        <div class="chart-container2">
            <canvas id="brokerPieChart"></canvas>
        </div>
    </div>
 <canvas id="monthlyBookingsChart" width="400" height="200"></canvas>


</div>

    </div>
</div>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('monthlyIncomeChart').getContext('2d');
    var monthlyIncomeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($boarding_houses); ?>,
            datasets: [{
                label: 'Monthly Income',
                data: <?php echo json_encode($monthly_incomes); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true, // Start y-axis at 0
                    ticks: {
                        stepSize: 1000, // Increment step size by 1000
                        callback: function(value) {
                            return '' + value; // Prefix with $ sign
                        }
                    }
                }
            },
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });

    // Pie Chart for Brokers Percentage
    var ctxBroker = document.getElementById('brokerPieChart').getContext('2d');
    var brokerPieChart = new Chart(ctxBroker, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($broker_labels); ?>,
            datasets: [{
                label: 'Brokers Percentage',
                data: <?php echo json_encode($broker_percentages); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 159, 64, 0.5)'
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
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + Math.round(tooltipItem.raw) + '%';
                        }
                    }
                }
            }
        }
    });
     // Line Chart for Monthly Income
    var ctxLine = document.getElementById('monthlyIncomeLineChart').getContext('2d');
    var monthlyIncomeLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Monthly Income',
                data: <?php echo json_encode(array_values($monthly_total_income)); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1000,
                        callback: function(value) {
                            return '' + value;
                        }
                    }
                }
            },
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });
});
const ctx = document.getElementById('monthlyBookingsChart').getContext('2d');
    
    const monthlyBookings = <?php echo json_encode(array_values($monthly_bookings)); ?>;
    const labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    
    const monthlyBookingsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Bookings per Month',
                data: monthlyBookings,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total Bookings'
                    },
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1, // Ensure ticks are whole numbers
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                        }
                    }
                }
            }
        }
    });
</script>
