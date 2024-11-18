<?php include('header.php'); ?>
<?php 
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

<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>
    
    <div class="main-content"><br><br>
        <h3>Dashboard</h3>
        
        <div class="dashboard-cards">
            <!-- Boarding House Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">
                            <?php
                            $result = mysqli_query($dbconnection, "SELECT count(1) FROM rental WHERE register1_id ='$login_session'");
                            if ($result) {
                                $row = mysqli_fetch_array($result);
                                echo $row[0];
                            }
                            ?>
                        </div>
                        <div class="font-14 text-secondary weight-500">Boarding House</div>
                    </div>
                    <div class="widget-icon">
                        <i class="fa fa-home animated-icon" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <!-- Requesting Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">
                            <?php
                            $result = mysqli_query($dbconnection, "SELECT count(*) FROM book WHERE register1_id='$login_session' AND status=''");
                            if ($result) {
                                $row = mysqli_fetch_array($result);
                                echo $row[0];
                            }
                            ?>
                        </div>
                        <div class="font-14 text-secondary weight-500">Requesting</div>
                    </div>
                    <div class="widget-icon">
                        <i class="fa fa-envelope animated-icon" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <!-- Confirmed Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">
                            <?php
                            $result = mysqli_query($dbconnection, "SELECT COUNT(*) FROM book WHERE register1_id='$login_session' AND status='Confirm'");
                            if ($result) {
                                $row = mysqli_fetch_array($result);
                                echo $row[0];
                            }
                            ?>
                        </div>
                        <div class="font-14 text-secondary weight-500">Confirmed</div>
                    </div>
                    <div class="widget-icon">
                        <i class="fa fa-thumbs-o-up animated-icon" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <!-- Total Monthly Income Card -->
            <div class="card-box">
                <div class="widget-style3">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">
                            <?php echo number_format($total_income); ?>
                        </div>
                        <div class="font-14 text-secondary weight-500">Total Monthly Income</div>
                    </div>
                    <div class="widget-icon">
                        <i class="fa fa-money animated-icon" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div> <br />

        <div class="row">
     <div class="container">
        <h3>Boarding House Monthly Income</h3>
        <div class="chart-container1">
            <canvas id="monthlyIncomeChart" ></canvas></div>
        </div>
    
          
                    
<div class="container">
    <h3>Boarding House Ratings</h3>
    <canvas id="monthlyBookingsChart"></canvas>
</div>

</div>

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
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                <?php } ?>
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1000,
                        callback: function(value) {
                            return '' + value; // Customize axis labels
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
