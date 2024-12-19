<?php include('header.php'); ?>



<?php 


if (isset($_GET['pageno'])) {
  $pageno = $_GET['pageno'];
} else {
  $pageno = 1;
}
  $no_of_records_per_page = 6;
  $offset = ($pageno-1) * $no_of_records_per_page;

  $total_pages_sql = "SELECT COUNT(*) FROM register1";
  $result_pages = mysqli_query($dbconnection,$total_pages_sql);
  $total_rows = mysqli_fetch_array($result_pages)[0];
  $total_pages = ceil($total_rows / $no_of_records_per_page);

$sql_show="SELECT * FROM rental LIMIT $offset, $no_of_records_per_page";



if(isset($_POST["search"])) {

  $query = $_POST['query'];
  $sql_show="SELECT * FROM rental WHERE (`address` LIKE '%".$query."%')";

}

?>


<div class="bigbanner">
  <br />
  <br />
  <br /><br>
  <h2 class="tagline">A PERFECT PLACE TO FIND YOUR PERFECT BHOUSE</h2>
 <center>
  <form action="" method="POST">
    <div class="input-group" id="searchbox">
      <input name="query" type="text" class="form-control" placeholder="Search your barangay">
    <button name="search" class="btn" type="submit">
  Search <i class="fa fa-search rotating-icon"></i>
</button>

    </div>
  </form>
</center>
</div>
<br><br>
<style>
.rotating-icon {
  display: inline-block;
  animation: sideward-rotate 5s infinite ease-in-out;
}

@keyframes sideward-rotate {
  0%, 100% {
    transform: rotateY(0deg); /* Default position */
  }
  25% {
    transform: rotateY(90deg); /* Midway rotation */
  }
  50% {
    transform: rotateY(180deg); /* Flipped completely */
  }
  75% {
    transform: rotateY(270deg); /* Almost back */
  }
}

#searchbox {
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 30px;
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

#searchbox input {
    border: none;
    flex: 1;
    padding: 12px 20px;
    font-size: 1rem;
    font-family: 'Roboto', sans-serif;
    color: #333333;
    border-radius: 30px 0 0 30px;
    outline: none;
}

#searchbox input::placeholder {
    color: #999999;
    font-size: 0.9rem;
}

#searchbox button {
    background: linear-gradient(90deg, #007bff, #0056b3);
    color: #ffffff;
    font-size: 1rem;
    font-family: 'Poppins', sans-serif;
    border: none;
    padding: 12px 20px;
    border-radius: 0 30px 30px 0;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

#searchbox button:hover {
    background: linear-gradient(90deg, #0056b3, #003f7f);
}

#searchbox button i {
    margin-left: 8px;
    font-size: 1rem;
}

#searchbox button:focus {
    outline: none;
}

@media (max-width: 768px) {
    #searchbox {
        flex-direction: column;
        border-radius: 10px;
    }

    #searchbox input, #searchbox button {
        border-radius: 0;
        width: 100%;
    }

    #searchbox button {
        border-radius: 0 0 10px 10px;
    }
}
/* Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500&display=swap');

body {
    font-family: 'Roboto', sans-serif;
    background-color: #eff3ea;
    margin: 0;
    padding: 0;
}

.course_card {
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 24px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.course_card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.course_card_img img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.course_card_content {
    padding: 16px;
    text-align: center;
    height: 200px;
}

.course_card_content h5 {
    font-family: 'Poppins', sans-serif;
    font-size: 1.5rem;
    font-weight: 600;
    color: #333333;
    margin-bottom: 12px;
}

.course_card_content .price {
    font-size: 1.1rem;
    color: #ff5722;
    font-weight: 500;
    margin-bottom: 8px;
}

.course_card_content .address {
    font-size: 0.9rem;
    color: #777777;
    margin-bottom: 12px;
}

.course_card_content .slots {
    font-size: 0.9rem;
    color: #555555;
    margin-top: 8px;
}

.course_card_content .slots i {
    color: #007bff;
    margin-right: 5px;
}

.course_card_footer {
    padding: 16px;
    background-color: #f9f9f9;
    border-top: 1px solid #ececec;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.course_card_footer .ratingshome {
    color: #ffc107;
    font-size: 1.2rem;
}

.course_card_footer .btn {
    padding: 10px 20px;
    font-size: 0.9rem;
    font-weight: 500;
    color: #ffffff;
    background-color: #007bff;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.course_card_footer .btn:hover {
    background-color: #0056b3;
}

.course_card_footer .btn.disabled {
    background-color: #b0b0b0;
    pointer-events: none;
}
.pagination a {
    color: #007bff;
    padding: 10px 15px;
    margin: 0 5px;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.3s ease;
    display: inline-flex;
    align-items: center; /* Align icon and text vertically */
}

.pagination a i {
    margin-right: 5px; /* Adds space between icon and text */
}

.pagination a:hover {
    background: #007bff;
    color: #ffffff;
}

.pagination a.disabled {
    pointer-events: none;
    color: #cccccc;
    border-color: #cccccc;
}

.pagination li.disabled a {
    cursor: not-allowed;
}

</style>
<br><br><br>
<div class="container">
    <div class="row mx-auto" id="recent">
        <?php

        include('encryption_helper.php'); // Include encryption helper file
$encryption_key = 'YourSecureKeyHere';
        $sql = $sql_show;
        $result = mysqli_query($dbconnection, $sql);
      while ($row = $result->fetch_assoc()) {
    $rent_id = $row['rental_id'];

    // Calculate available slots
$sql_book = "SELECT COUNT(*) AS booked_count FROM booking b 
              JOIN payment p ON b.payment_id = p.payment_id
              WHERE p.rental_id = ? AND b.status='Confirm'";
$stmt_book = $dbconnection->prepare($sql_book);
$stmt_book->bind_param("i", $rent_id);
$stmt_book->execute();
$result_book = $stmt_book->get_result();
$row_book = $result_book->fetch_array();
$reserved = $row_book['booked_count'];
$stmt_book->close();

$available_slots = $row['slots'] - $reserved;


    // Encrypt the rental_id for the link
    $encrypted_rental_id = encrypt($rent_id, $encryption_key);
?>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="course_card">
                <div class="course_card_img">
                    <img src="uploads/<?php echo $row['photo']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                </div>
                <div class="course_card_content">
                    <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                    <div class="price">₱ <?php echo number_format($row['monthly'], 2); ?> / Monthly</div>
                    <div class="address"><?php echo htmlspecialchars($row['address']); ?></div>
                    <div class="slots">
                        <i class="fa fa-bed"></i> <?php echo $available_slots; ?> Slots Available
                    </div>
                </div>
               <div class="course_card_footer">
    <?php
    // Fetch the sum and count of ratings for the current rental
   $sql_rating = "
    SELECT 
        SUM(b.ratings) AS totalrating, 
        COUNT(b.ratings) AS ratingcount 
    FROM booking b
    INNER JOIN payment p ON b.payment_id = p.payment_id
    INNER JOIN rental r ON p.rental_id = r.rental_id
    WHERE r.rental_id = '$rent_id' AND b.ratings > 0;
";

    $result_rating = $dbconnection->query($sql_rating);
    
    $totalrating = 0;
    $ratingcount = 0;

    if ($result_rating && $row_rating = $result_rating->fetch_assoc()) {
        $totalrating = (float)$row_rating['totalrating'];
        $ratingcount = (int)$row_rating['ratingcount'];
    }

    // Calculate the average rating
    $averageRating = ($ratingcount > 0) ? $totalrating / $ratingcount : 0;
    
    ?>
    
    <span class="ratingshome">
        <?php
        // Display average rating as stars (rounded to the nearest whole number)
        $roundedRating = round($averageRating); // Round to the nearest integer
        for ($i = 1; $i <= 5; $i++) {
            echo ($i <= $roundedRating ? "★" : "☆");
        }
        ?>
    </span>
    <a class="btn <?php echo $available_slots == 0 ? 'disabled' : ''; ?>" 
       href="<?php echo $available_slots > 0 ? 'view.php?rental_id=' . urlencode($encrypted_rental_id) : '#'; ?>">
       <?php echo $available_slots == 0 ? 'Fully Booked' : 'Book Now'; ?>
    </a>
</div>

            </div>
        </div>
        <?php } ?>
    </div>
<br><br>
    <center>
       <ul class="pagination">
    <li class="<?php if ($pageno <= 1) echo 'disabled'; ?>">
        <a href="<?php if ($pageno > 1) echo "?pageno=" . ($pageno - 1) . "#recent"; ?>">
            <i class="fa fa-chevron-left"></i> Prev
        </a>
    </li>
    <li class="<?php if ($pageno >= $total_pages) echo 'disabled'; ?>">
        <a href="<?php if ($pageno < $total_pages) echo "?pageno=" . ($pageno + 1) . "#recent"; ?>">
            Next <i class="fa fa-chevron-right"></i>
        </a>
    </li>
</ul>

    </center>
</div>

<?php include('footer.php'); ?>
