<?php
// book.php
include('connection.php');

// Get the rental ID from the URL
$rental_id = $_GET['bh_id'];

// Fetch rental details
$sql_register1 = "SELECT * FROM rental WHERE rental_id='$rental_id'";
$result_register1 = mysqli_query($dbconnection, $sql_register1);
$row_register1 = $result_register1->fetch_assoc();
$register1_id = $row_register1['id'];

if (isset($_POST["booknow"])) {
    // Escape user inputs for security
    $firstname = mysqli_real_escape_string($dbconnection, $_POST['firstname']);
    $middlename = mysqli_real_escape_string($dbconnection, $_POST['middlename']);
    $lastname = mysqli_real_escape_string($dbconnection, $_POST['lastname']);
    $age = mysqli_real_escape_string($dbconnection, $_POST['age']);
    $gender = mysqli_real_escape_string($dbconnection, $_POST['gender']);
    $gcash_number = mysqli_real_escape_string($dbconnection, $_POST['gcash_number']);
    $email = mysqli_real_escape_string($dbconnection, $_POST['email']);
    $address = mysqli_real_escape_string($dbconnection, $_POST['Address']);
    
    // Sanitize inputs
    $firstname_sanitized = htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8');
    $lastname_sanitized = htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8');
    $middlename_sanitized = htmlspecialchars($middlename, ENT_QUOTES, 'UTF-8');
    $address_sanitized = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');

    // Regular expression for name validation (letters, hyphens, apostrophes, spaces)
    $name_regex = "/^[A-Za-z\s'-]+$/";
    // Regular expression for GCash number validation
    $gcash_regex = "/^\+63\d{10}$/";

    // Validate the first name
    if (!preg_match($name_regex, $firstname)) {
        echo '<script>Swal.fire("Invalid input", "Please enter a valid first name.", "error");</script>';
    } elseif (!preg_match($name_regex, $lastname)) {
        echo '<script>Swal.fire("Invalid input", "Please enter a valid last name.", "error");</script>';
    } elseif (!preg_match($name_regex, $address)) {
        echo '<script>Swal.fire("Invalid input", "Please enter a valid Address.", "error");</script>';
    } elseif (!preg_match($gcash_regex, $gcash_number)) {
        echo '<script>Swal.fire("Invalid input", "Please enter a valid GCash number. It should start with +63 and be followed by 10 digits.", "error");</script>';
    } else {
        // Proceed with file upload if names and GCash number are valid
        $gcash_picture = $_FILES['gcash_picture'];
        $target_dir = "uploads/gcash_pictures/";
        $target_file = $target_dir . basename($gcash_picture["name"]);

        if (move_uploaded_file($gcash_picture["tmp_name"], $target_file)) {
            $sql_book = "INSERT INTO book (firstname, middlename, lastname, age, gender, contact_number, email, register1_id, bhouse_id, Address, gcash_picture)
                         VALUES ('$firstname_sanitized', '$middlename_sanitized', '$lastname_sanitized', '$age', '$gender', '$gcash_number', '$email', '$register1_id', '$rental_id', '$address', '$target_file')";

            if ($dbconnection->query($sql_book) === TRUE) {
                echo '<script>Swal.fire("Success", "Successfully Booked. Please complete your payment.", "success");</script>';
            } else {
                echo '<script>Swal.fire("Error", "Error in database: ' . $dbconnection->error . '", "error");</script>';
            }
        } else {
            echo '<script>Swal.fire("Error", "Error uploading file.", "error");</script>';
        }
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// JavaScript function to validate name fields (letters, hyphens, apostrophes, spaces only)
function validateNameField(fieldId, message) {
    var field = document.getElementById(fieldId);
    var value = field.value;
    var regex = /^[A-Za-z\s'-]+$/;

    if (!regex.test(value)) {
        field.setCustomValidity(message);
    } else {
        field.setCustomValidity('');
    }
}

// Attach validation to input fields
document.getElementById('firstname').addEventListener('input', function() {
    validateNameField('firstname', 'Please enter a valid first name.');
});
document.getElementById('lastname').addEventListener('input', function() {
    validateNameField('lastname', 'Please enter a valid last name.');
});
document.getElementById('middlename').addEventListener('input', function() {
    validateNameField('middlename', 'Please enter a valid middle name.');
});
document.getElementById('Address').addEventListener('input', function() {
    validateNameField('Address', 'Please enter a valid Address.');
});
</script>

<style>
/* Your existing CSS */
.container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
.form-group { margin-bottom: 20px; }
.form-control { width: 100%; padding: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px; }
.btn-primary { background-color: #007bff; border: none; }
.btn-primary:hover { background-color: #0069d9; }
h2 { text-align: center; margin-bottom: 20px; }
@media (max-width: 768px) { .container { width: 100%; } }
</style>

<div class="container">
    <h2>Book Now</h2>
    <form method="POST" action="book.php?bh_id=<?php echo $rental_id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="firstname">First Name:</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>
        <div class="form-group">
            <label for="middlename">Middle Name:</label>
            <input type="text" class="form-control" id="middlename" name="middlename">
        </div>
        <div class="form-group">
            <label for="lastname">Last Name:</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>
        <div class="form-group">
            <label for="age">Age:</label>
            <input type="number" class="form-control" id="age" name="age" required min="1" max="120" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0,3);">
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="Address">Address:</label>
            <input type="text" class="form-control" id="Address" name="Address" required>
        </div>
       <div class="form-group">
        <label for="gcash_number">GCash Number:</label>
        <input type="text" class="form-control" id="gcash_number" name="gcash_number" required pattern="^\+63\d{10}$" title="GCash number must start with +63 and be followed by 10 digits.">
        </div>

        <div class="form-group">
            <label for="gcash_picture">GCash Picture Reference:<br> For downpayment</label>
            <input type="file" class="form-control" id="gcash_picture" name="gcash_picture" required>
        </div>
        <button type="submit" name="booknow" class="btn btn-primary">Book Now</button>
    </form>
</div>

<?php include('footer.php'); ?>
