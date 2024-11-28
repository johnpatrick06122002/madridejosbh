<?php include('header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php 
// Step 1: Sanitize and validate rental ID
$rental_id = isset($_GET['bh_id']) ? intval($_GET['bh_id']) : 0;
$sql_register1 = "SELECT * FROM rental WHERE rental_id='$rental_id'";

$result_register1 = mysqli_query($dbconnection, $sql_register1);
if (!$result_register1) {
    die("Query Failed: " . mysqli_error($dbconnection));
}

while ($row_register1 = $result_register1->fetch_assoc()) {
    $register1_id = $row_register1['id'];
}

if (isset($_POST['submitfeedback'])) {
    // Step 1: Sanitize the email input
    $boarderEmail = mysqli_real_escape_string($dbconnection, $_POST['boarderemail']);
    
    // Step 2: Check if the email exists in the `book` table
    $sqlfdbck = "SELECT * FROM book WHERE email = '$boarderEmail'";
    $resultfdbck = mysqli_query($dbconnection, $sqlfdbck);
    
    if (!$resultfdbck) {
        die("Query Failed: " . mysqli_error($dbconnection));
    }

    $countfdbck = mysqli_num_rows($resultfdbck);

    if ($countfdbck == 1) {
        // Step 3: Sanitize and validate feedback inputs
        $ratings = floatval($_POST['rate']); // Ensure the rating is treated as a float
        $feedback = mysqli_real_escape_string($dbconnection, $_POST['feedbackmsg']);
        
        // Step 4: Update the rating and feedback based on `email`
        $update_query = "UPDATE book SET ratings = '$ratings', feedback = '$feedback' WHERE email = '$boarderEmail'";
        
        if (mysqli_query($dbconnection, $update_query)) {
            echo '<script type="text/javascript">
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Your feedback has been submitted."
                });
            </script>';
        } else {
            echo '<script type="text/javascript">
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "There was an error submitting your feedback. Please try again later."
                });
            </script>';
        }
    } else {
        echo '<script type="text/javascript">
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Sorry! We couldn\'t find your email. You might not be a boarder here or your registered email is incorrect."
            });
        </script>';
    }
}
?>
<br><br>

 
<div class="container">
<br />
<br />

<?php
$sql = "SELECT * FROM rental WHERE rental_id='$rental_id'";
$result = mysqli_query($dbconnection, $sql);
while ($row = $result->fetch_assoc()) {
    $register1id = $row['register1_id'];
?>
<br />
<h2><?php echo $row['title']; ?></h2>
<div class="wrap">
        <div class="gallery">
        <?php 
        $sql_gallery = "SELECT * FROM gallery WHERE rental_id='$rental_id'";
        $result_gallery = mysqli_query($dbconnection, $sql_gallery);
        while ($row_gallery = $result_gallery->fetch_assoc()) { ?>
            <a href="uploads/<?php echo $row_gallery['file_name']; ?>"><img src="uploads/<?php echo $row_gallery['file_name']; ?>"></a>
        <?php } ?>
    </div>
</div>
<div class="slidebtn">
    <button class="prev"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
    <button class="next"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
</div>
<br />
<h5>₱ <?php echo number_format($row['monthly'], 2); ?> / Monthly</h5>
<h6><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo htmlspecialchars($row['address']); ?></h6>

<h6>
    Payment Policy: 
    <?php 
        if ($row['downpayment_amount'] !== null && $row['downpayment_amount'] > 0) {
            echo "Downpayment"; 
        } elseif ($row['installment_months'] !== null && $row['installment_months'] > 0 && $row['installment_amount'] !== null) {
            echo "Installment"; 
        } else {
            echo "No payment policy available";
        }
    ?><br>
    <?php if ($row['downpayment_amount'] > 0): ?>
        Amount: ₱ <?php echo number_format($row['downpayment_amount'], 2); ?>
    <?php elseif ($row['installment_months'] > 0 && $row['installment_amount'] > 0): ?>
        Amount: ₱ <?php echo number_format($row['installment_amount'], 2); ?><br>
        Installment Months: <?php echo htmlspecialchars($row['installment_months']); ?>
    <?php endif; ?>
   
</h6>



<br />
<br />


<?php 
$freewifi = $row['wifi'] == 'yes' ? '<i class="fa fa-check-circle text-success" aria-hidden="true"></i>' : '<i class="fa fa-times-circle text-danger" aria-hidden="true"></i>';
$freewater = $row['water'] == 'yes' ? '<i class="fa fa-check-circle text-success" aria-hidden="true"></i>' : '<i class="fa fa-times-circle text-danger" aria-hidden="true"></i>';
$freekuryente = $row['kuryente'] == 'yes' ? '<i class="fa fa-check-circle text-success" aria-hidden="true"></i>' : '<i class="fa fa-times-circle text-danger" aria-hidden="true"></i>';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
<style>
/* Add these rules at the top of your existing CSS */
html, body {
    overflow-x: hidden;
    width: 100%;
    margin: 0;
    padding: 0;
    position: relative;
}
    /* Make sure all containers respect viewport width */
.container, .container-fluid {
    padding-right: 15px;
    padding-left: 15px;
    width: 100%;
    box-sizing: border-box;
}

 
 
    /* Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500&display=swap');

.reviews {
    margin-top: 40px;
}

.reviews h2 {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    color: #333333;
    margin-bottom: 20px;
}

.card-review {
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    width: 95%;
    margin-left:15px;
}

.card-review:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.card-header {
    background-color: #f9f9f9;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid #007bff;
}

.card-header b {
    font-family: 'Poppins', sans-serif;
    color: #007bff;
    font-size: 1rem;
}

.card-header small {
    font-family: 'Roboto', sans-serif;
    color: #999999;
    font-size: 0.85rem;
}

.card-header .ratings {
    color: #ffc107;
    font-size: 1rem;
}

.card-body {
    padding: 15px;
    font-family: 'Roboto', sans-serif;
    color: #555555;
    font-size: 0.95rem;
    text-align: justify;
}

.card-body p {
    margin: 0;
    line-height: 1.4;
}

.text-muted {
    color: #888888 !important;
}

.stretched-link {
    position: relative;
    z-index: 1;
}

.text-center {
    text-align: center;
}

.text-muted {
    font-size: 0.9rem;
}
/* Map Container to maintain 16:9 aspect ratio */
.map-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    height: 0;
    overflow: hidden;
    max-width: 100%;
    margin: auto;
}

/* Responsive Map Iframe */
.responsive-map {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}
 
    h5 {
        font-size: 1.5rem; /* Larger font size for emphasis */
        font-weight: bold; /* Bold to stand out */
        color: #fff; /* White text color for contrast */
        background: linear-gradient(90deg, #007bff, #0056b3); /* Blue gradient background */
        padding: 12px 15px; /* Padding for better spacing */
        border-radius: 8px; /* Rounded corners for a modern look */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
        margin-bottom: 20px; /* Adequate spacing between elements */
        text-align: center; /* Center align the text */
    }

    h6 {
        font-size: 1.1rem; /* Slightly larger font for readability */
        font-weight: 600; /* Semi-bold for emphasis */
        color: #343a40; /* Dark gray for a professional look */
        background-color: #f8f9fa; /* Light background for contrast */
        padding: 10px 15px; /* Padding for a clean layout */
        border-left: 6px solid #28a745; /* Green border for highlighting */
        border-radius: 5px; /* Rounded corners */
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        margin-bottom: 15px; /* Consistent spacing */
    }

    /* Optional hover effect for h5 */
    h5:hover {
        transform: scale(1.02); /* Slight zoom on hover */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* More pronounced shadow */
        transition: all 0.3s ease; /* Smooth transition */
    }

    /* Optional hover effect for h6 */
    h6:hover {
        background-color: #e2f0e9; /* Lighter green background on hover */
        border-left-color: #218838; /* Darker green border on hover */
        transition: background-color 0.3s ease, border-left-color 0.3s ease; /* Smooth hover effect */
    }
  /* Style for feature cards */
    .feature-card {
        font-size: 1.2rem; /* Slightly larger font for readability */
        font-weight: 600; /* Bold text for emphasis */
        color: #fff; /* White text for contrast */
        background: linear-gradient(90deg, #28a745, #218838); /* Green gradient background */
        padding: 15px 20px; /* Spacious padding */
        border-radius: 8px; /* Rounded corners for a modern look */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
        display: flex; /* Flexbox for alignment */
        align-items: center; /* Vertically align content */
        justify-content: center; /* Center the content */
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
    }

    /* Icon styling for the feature cards */
    .feature-card i {
        font-size: 1.5rem; /* Larger icons */
        margin-right: 10px; /* Space between icon and text */
    }

    /* Hover effect for feature cards */
    .feature-card:hover {
        transform: scale(1.05); /* Slight zoom on hover */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Enhanced shadow on hover */
    }

   /* Responsive adjustments */
@media (max-width: 768px) {
    .feature-card {
        font-size: 1rem; /* Adjust font size for smaller screens */
        padding: 10px 15px; /* Reduce padding for compact layout */
        margin-bottom: 15px; /* Add space between stacked feature cards */
    }
    
    /* Optional: If you want to remove bottom margin from the last feature card */
    .feature-card:last-child {
        margin-bottom: 5px; /* Removes margin from the last feature card */
    }
}

/* Style for the Description Heading */
    h3 {
        font-size: 1.8rem; /* Slightly larger font for emphasis */
        font-weight: bold; /* Bold text for prominence */
        color: #fff; /* White text color for contrast */
        background: linear-gradient(90deg, #17a2b8, #138496); /* Blue-green gradient background */
        padding: 12px 15px; /* Padding for better spacing */
        border-radius: 8px; /* Rounded corners for a modern look */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
        margin-bottom: 20px; /* Spacing below the heading */
        text-align: center; /* Center align the text */
    }

    /* Style for the Description Card */
    .description-card {
        background-color: #f8f9fa; /* Light gray background */
        border: 1px solid #dee2e6; /* Subtle border */
        border-radius: 10px; /* Rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow for depth */
        padding: 20px; /* Ample padding inside the card */
        font-size: 1.1rem; /* Slightly larger font for readability */
        line-height: 1.6; /* Improve text spacing */
        color: #495057; /* Dark gray text for professional appearance */
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
    }

    /* Hover effect for Description Card */
    .description-card:hover {
        transform: scale(1.02); /* Slight zoom on hover */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
    }

      /* Card Container for Landlord Info */
    .landlord-info-card {
        background-color: #f8f9fa; /* Light background for consistency */
        border: 1px solid #dee2e6; /* Subtle border for structure */
        border-radius: 10px; /* Rounded corners for a modern look */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add depth with shadow */
        padding: 20px; /* Spacious padding */
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
        font-size: 1rem; /* Ensure readability */
        margin-bottom: 20px; /* Spacing between cards */
    }

    .landlord-info-card:hover {
        transform: scale(1.02); /* Slight zoom effect */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
    }

    /* Styling for Icons in Landlord Info */
    .landlord-info-card i {
        font-size: 1.8rem; /* Larger icon size for emphasis */
        color: #007bff; /* Blue for branding consistency */
    }

    /* Content Alignment in Landlord Info */
    .landlord-info-card .row {
        align-items: center; /* Vertically center icon and text */
    }

    /* Separator Lines */
    .landlord-info-card hr {
        border-top: 1px solid #e9ecef; /* Subtle divider line */
        margin: 15px 0; /* Consistent spacing */
    }

    /* Book Now Button */
    .btn-primary {
        background: linear-gradient(90deg, #007bff, #0056b3); /* Gradient for vibrancy */
        color: #fff; /* White text for contrast */
        border: none; /* Clean borderless design */
        border-radius: 8px; /* Rounded corners */
        padding: 10px 20px; /* Spacious padding */
        font-weight: bold; /* Emphasis on text */
        margin-top: 10px; /* Spacing above */
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
    }

    .btn-primary:hover {
        transform: scale(1.05); /* Slight zoom on hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Depth on hover */
    }

    /* Feedback Button */
    .btn-danger {
        background: linear-gradient(90deg, #dc3545, #c82333); /* Gradient for danger */
        color: #fff; /* White text */
        border: none; /* Clean borderless design */
        border-radius: 8px; /* Rounded corners */
        padding: 10px 20px; /* Spacious padding */
        font-weight: bold; /* Emphasis on text */
        margin-top: 10px; /* Spacing above */
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
    }

    .btn-danger:hover {
        transform: scale(1.05); /* Slight zoom on hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Depth on hover */
    }
</style>
</head>
<body>
<div class="row text-center">
    <div class="col">
        <div class="feature-card">
            <i class="fa fa-wifi" aria-hidden="true"></i> FREE WIFI <?php echo $freewifi; ?>
        </div>
    </div>
    <div class="col">
        <div class="feature-card">
            <i class="fa fa-tint" aria-hidden="true"></i> FREE WATER <?php echo $freewater; ?>
        </div>
    </div>
    <div class="col">
        <div class="feature-card">
            <i class="fa fa-lightbulb-o" aria-hidden="true"></i> FREE KURYENTE <?php echo $freekuryente; ?>
        </div>
    </div>
</div>
<br />
<br />

<div class="row">
   <div class="col-md-8">
    <h3>Description</h3>
    <div class="card mb-4 description-card">
        <div class="card-body">
            <?php echo $row['description']; ?>
        </div>
    </div>

     
<div class="map-container">
    <iframe 
        class="responsive-map" 
        src="<?php echo $row['map']; ?>" 
        allowfullscreen 
        loading="lazy">
    </iframe>
</div>

    </div>
   <div class="col-md-4"> 
    <br>
    <h3>Landlord's INFO</h3>
<?php
 
// Fetch Landlord's details
$sql_ll = "SELECT CONCAT(firstname, ' ', COALESCE(middlename, ''), ' ', lastname) AS name, email, contact_number, profile_photo 
           FROM register2 
           JOIN register1 ON register2.register1_id = register1.id 
           WHERE register1_id = ?";

// Use prepared statement to prevent SQL injection
if ($stmt = $dbconnection->prepare($sql_ll)) {
    $stmt->bind_param("i", $register1id); // Assuming $register1id is an integer
    $stmt->execute();
    $result_ll = $stmt->get_result();

    if ($result_ll && $row_ll = $result_ll->fetch_assoc()) {
        $name = $row_ll['name'];
        $email = $row_ll['email'];
        $contact_number = $row_ll['contact_number'];
        $profile_photo = $row_ll['profile_photo'];
    } else {
        echo "Error fetching register1 details.";
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $dbconnection->error;
}


// Fetch rental details to check slots
$sql_rental = "SELECT CAST(slots AS UNSIGNED) AS slots FROM rental WHERE rental_id = ?";
if ($stmt_rental = $dbconnection->prepare($sql_rental)) {
    $stmt_rental->bind_param("i", $rental_id); // Assuming $rental_id is an integer
    $stmt_rental->execute();
    $result_rental = $stmt_rental->get_result();

    if ($result_rental && $rental = $result_rental->fetch_assoc()) {
        $availableSlots = (int) $rental['slots']; // Cast to integer

        // Fetch current number of bookings
        $sql_bookings = "SELECT COUNT(*) AS booked_count FROM book WHERE bhouse_id = ?";
        if ($stmt_bookings = $dbconnection->prepare($sql_bookings)) {
            $stmt_bookings->bind_param("i", $rental_id); // Assuming $rental_id is used for bookings as well
            $stmt_bookings->execute();
            $result_bookings = $stmt_bookings->get_result();

            if ($result_bookings && $bookings = $result_bookings->fetch_assoc()) {
                $bookedCount = (int) $bookings['booked_count'];
                $slotsAvailable = $availableSlots - $bookedCount;
                $bookNowButtonDisabled = $slotsAvailable <= 0; // Disable button if no slots available
            } else {
                echo "Error fetching booking details.";
                $bookNowButtonDisabled = true; // Default to disabled if there’s an error
            }
            $stmt_bookings->close();
        } else {
            echo "Error preparing bookings statement: " . $dbconnection->error;
        }
    } else {
        echo "Error fetching rental details.";
    }
    $stmt_rental->close();
} else {
    echo "Error preparing rental statement: " . $dbconnection->error;
}

?>
 <div class="landlord-info-card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <p class="mb-0"><i class="fa fa-user" aria-hidden="true"></i></p>
                </div>
                <div class="col-sm-9">
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($name); ?></p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-3">
                    <p class="mb-0"><i class="fa fa-envelope" aria-hidden="true"></i></p>
                </div>
                <div class="col-sm-9">
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-3">
                    <p class="mb-0"><i class="fa fa-phone-square" aria-hidden="true"></i></p>
                </div>
                <div class="col-sm-9">
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($contact_number); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <a href="book.php?bh_id=<?php echo $rental_id; ?>" class="btn btn-primary">
        BOOK NOW
    </a>
    <button data-toggle="modal" data-target="#feedback" class="btn btn-danger">FEEDBACK</button>
</div>
    </div>
</div>

<br>
<hr>
<br>

<div class="reviews">
    <h2 class="text-center">Boarders Review</h2>
    <div class="row">
        <?php
        // Fetch reviews with ratings > 0
        $sqlreview = "SELECT * FROM book WHERE ratings IS NOT NULL AND ratings > 0 AND bhouse_id = '$rental_id'";
        $resultreview = mysqli_query($dbconnection, $sqlreview);

        if (mysqli_num_rows($resultreview) > 0) {
            while ($rowreview = $resultreview->fetch_assoc()) {
                $name = $rowreview['firstname'] . ' ' . $rowreview['lastname'];
                $feedback = $rowreview['feedback'];
                $date = $rowreview['date_posted'];
                $ratings = $rowreview['ratings'];
        ?>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card card-review">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <img class="rounded-circle me-2" 
                             src="https://www.worldfuturecouncil.org/wp-content/uploads/2020/06/blank-profile-picture-973460_1280-1-705x705.png" 
                             alt="Reviewer Image">
                        <div class="ms-2">
                            <b><?php echo $name; ?></b>
                            <br>
                            <small><?php echo $date; ?></small>
                        </div>
                    </div>
                    <div>
                        <select name="star_rating_option" class="ratings" data-fratings="<?php echo $ratings; ?>" disabled>
                            <option value="1" <?php if ($ratings == 1) echo 'selected'; ?>>★☆☆☆☆</option>
                            <option value="2" <?php if ($ratings == 2) echo 'selected'; ?>>★★☆☆☆</option>
                            <option value="3" <?php if ($ratings == 3) echo 'selected'; ?>>★★★☆☆</option>
                            <option value="4" <?php if ($ratings == 4) echo 'selected'; ?>>★★★★☆</option>
                            <option value="5" <?php if ($ratings == 5) echo 'selected'; ?>>★★★★★</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <p><?php echo htmlspecialchars($feedback); ?></p>   
                </div>
                <a href="#" class="stretched-link"></a>
            </div>
        </div>
        <?php 
            }
        } else {
            echo '<div class="col-12"><p class="text-center text-muted">No reviews available.</p></div>';
        }
        ?>
    </div>
</div>



<?php } ?>

</div>

<!-- The Modal Feedback -->
<div class="modal" id="feedback">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-center">GIVE US A FEEDBACK</h3>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <span class="text-muted"><i class="fa fa-info-circle" aria-hidden="true"></i> We use your email to validate if you're a boarder</span>
                        <div class="input-group">
                            <input type="email" name="boarderemail" class="form-control" placeholder="Your Registered Email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <center>
                            <label for="rate" class="text-muted">Rate us:</label>
                            <select class="form-control torate" name="rate" id="rate" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </center>
                    </div>
                    <div class="form-group">
                        <label class="text-muted">Feedback:</label>
                        <textarea class="form-control" name="feedbackmsg" placeholder="Write your feedback here..." required></textarea>
                    </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <input type="submit" name="submitfeedback" class="btn btn-success" value="Submit Feedback">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times-circle" aria-hidden="true"></i> Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
</body>
   

<?php include('footer.php'); ?>
