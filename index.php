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
  <br />
  <h2 class="tagline">A PERFECT PLACE TO FIND YOUR PERFECT BHOUSE</h2>
  <center>
  <form action="" method="POST">
    <div class="input-group" id="searchbox">
    
    <input name="query" type="text" class="form-control" placeholder="Search your barangay">
    <div class="input-group-append">
      <button name="search" class="btn btn-secondary" type="submit">
        <i class="fa fa-search"></i>
      </button>

    </div>
  </div>
</form>
</center>
</div>
<br><br>
<style>
/* Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500&display=swap');

body {
    font-family: 'Roboto', sans-serif;
    background-color: #f4f5f7;
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

<div class="container">
    <div class="row mx-auto" id="recent">
        <?php
        $sql = $sql_show;
        $result = mysqli_query($dbconnection, $sql);
        while ($row = $result->fetch_assoc()) {
            $rent_id = $row['rental_id'];

            $result_book = mysqli_query($dbconnection, "SELECT COUNT(1) FROM book WHERE bhouse_id='$rent_id' AND status='Confirm'");
            $row_book = mysqli_fetch_array($result_book);
            $reserved = $row_book[0];
            $available_slots = $row['slots'] - $reserved;
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
                </div><br><br>
                <div class="course_card_footer">
                     <?php
                        // Sum all ratings for the current rental
                        $sql_rating = "SELECT SUM(ratings) as totalrating FROM book WHERE bhouse_id='$rent_id' AND ratings IS NOT NULL";
                        $result_rating = $dbconnection->query($sql_rating);
                        $totalrating = 0;
                        $count = 0;
                        while ($row_rating = $result_rating->fetch_assoc()) {
                            $totalrating = $row_rating['totalrating'];
                            $count++;
                        }
                        ?>
                    <span class="ratingshome">
                        <?php for ($i = 0; $i < 5; $i++) echo ($i < $totalrating ? "★" : "☆"); ?>
                    </span>
                    <a class="btn <?php echo $available_slots == 0 ? 'disabled' : ''; ?>" 
                       href="<?php echo $available_slots > 0 ? 'view.php?bh_id=' . $row['rental_id'] : '#'; ?>">
                       <?php echo $available_slots == 0 ? 'Fully Booked' : 'Book Now'; ?>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

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
