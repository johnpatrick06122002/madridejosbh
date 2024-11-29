<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../connection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['register1_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['register1_id'];

// Check if rental_id is provided in the GET request and is valid
if (!isset($_GET['rental_id']) || !is_numeric($_GET['rental_id'])) {
    die("Invalid rental ID.");
}

$rental_id = intval($_GET['rental_id']);

// Fetch rental details based on rental_id
$sql = "SELECT rental.*, register1.id AS register1_id 
        FROM rental 
        JOIN register1 ON rental.register1_id = register1.id 
        WHERE rental.rental_id = ?";
$stmt = $dbconnection->prepare($sql);
$stmt->bind_param('i', $rental_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No rental found for ID: " . $rental_id);
}

$row = $result->fetch_assoc();

$payment_type = (!empty($row['downpayment_amount']) && $row['downpayment_amount'] > 0) 
                ? 'downpayment' 
                : 'installment';
$paid_amount = $payment_type == 'downpayment' 
               ? $row['downpayment_amount'] 
               : $row['installment_amount'];

if (isset($_POST["booknow"])) {
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'] ?? '';
    $lastname = $_POST['lastname'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $address = $_POST['Address'];
    $contact_number = $_POST['contact_number'];

    if (empty($firstname) || empty($lastname) || empty($age) || 
        empty($gender) || empty($email) || empty($address) || empty($contact_number)) {
        die("All fields are required.");
    }

    $sql_book = "INSERT INTO book 
                 (firstname, middlename, lastname, age, gender, email, 
                  register1_id, bhouse_id, Address, contact_number, paid_amount, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_book = $dbconnection->prepare($sql_book);

    $status = 'Confirm';
    $stmt_book->bind_param('sssssssssdss', 
        $firstname, 
        $middlename, 
        $lastname, 
        $age, 
        $gender, 
        $email, 
        $row['register1_id'], 
        $rental_id, 
        $address,  
        $contact_number,
        $paid_amount,
        $status
    );

    if ($stmt_book->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Booking successful!',
                text: 'Your booking has been confirmed.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'dashboard.php';
                }
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Booking failed',
                text: '" . $stmt_book->error . "',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="x-icon" href="../b.png">
    <title>Booking Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        /* Form styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 90%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 1rem;
        }

        label {
            font-weight: 600;
            color: #5a5c69;
        }

        .btn-primary {
            background: #4e73df;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #2e59d9;
        }

        h3 {
            color: #5a5c69;
            font-weight: 500;
            font-size: 1.75rem;
            margin-bottom: 20px;
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
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar-container">
            <?php include('sidebar.php'); ?>
        </div>

        <div class="main-content"><br>
            <h3>BOOKING FORM</h3>
            <form method="POST" class="form-grid">
                <div class="form-group">
                    <label for="firstname">First Name</label><br>
                    <input type="text" id="firstname" name="firstname" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="middlename">Middle Name</label><br>
                    <input type="text" id="middlename" name="middlename" class="form-control">
                </div>

                <div class="form-group">
                    <label for="lastname">Last Name</label><br>
                    <input type="text" id="lastname" name="lastname" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="age">Age</label><br>
                    <input type="number" id="age" name="age" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label><br>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email">Email</label><br>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="Address">Address</label><br>
                    <input type="text" id="Address" name="Address" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="contact_number">Contact Number</label><br>
                    <input type="tel" id="contact_number" name="contact_number" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="payment_type">Payment Type</label><br>
                    <input type="text" id="payment_type" value="<?php echo htmlspecialchars($payment_type); ?>" class="form-control" readonly>
                    <input type="hidden" name="payment_type" value="<?php echo htmlspecialchars($payment_type); ?>">
                </div>

                <div class="form-group">
                    <label for="paid_amount">Amount</label><br>
                    <input type="text" id="paid_amount" value="<?php echo htmlspecialchars($paid_amount); ?>" class="form-control" readonly>
                    <input type="hidden" name="paid_amount" value="<?php echo htmlspecialchars($paid_amount); ?>">
                </div>

                <button type="submit" name="booknow" class="btn-primary">
                    <i class="fas fa-check-square"></i>
                    Book Now
                </button>
            </form>
        </div>
    </div>
</body>
</html>
