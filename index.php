<?php include('header.php'); ?>



<?php 


if (isset($_GET['pageno'])) {
  $pageno = $_GET['pageno'];
} else {
  $pageno = 1;
}
  $no_of_records_per_page = 6;
  $offset = ($pageno-1) * $no_of_records_per_page;

  $total_pages_sql = "SELECT COUNT(*) FROM landlords";
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


<div class="container">
<div class="row mx-auto" id="recent">

<?php


  $sql = $sql_show;
  $result = mysqli_query($dbconnection,$sql);
  while($row = $result->fetch_assoc()) {
    $rent_id = $row['rental_id'];
    $landlord_id = $row['landlord_id'];
?>

  <div class="col-lg-4 col-md-6 col-sm-12">
    <div class="course_card">
      <div class="course_card_img"><img src="uploads/<?php echo $row['photo']; ?>" style="height: 300px;object-fit: cover;"/></div>
      <div class="course_card_content">
        <h5 class="title"><?php echo $row['title']; ?></h5>

        <div class="row">
        <div class="col-sm-8">
        	 ₱ <?php echo $row['monthly']; ?> / Monthly <br />
        </div>
        <div class="col-md-4">

<?php
$result_book = mysqli_query($dbconnection,"SELECT count(1) FROM book WHERE bhouse_id='$rent_id' AND landlord_id='$landlord_id' AND status='Approved'");
$row_book = mysqli_fetch_array($result_book);
$reserved = $row_book[0];
?>

        	<i class="fa fa-bed" aria-hidden="true"></i> <?php echo $row['slots'] - $reserved; ?>
        </div>
        </div>

        <i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $row['address']; ?>
        <br />
        
      </div>
      <div class="course_card_footer">
        <div class="row">
        <div class="col-6">

<?php

//sum all ratings
$sql_rating = "SELECT SUM(ratings) as totalrating FROM book WHERE bhouse_id='$rent_id' AND ratings IS NOT NULL";
$result_rating = $dbconnection->query($sql_rating);
$count = 0;
  while($row_rating= $result_rating->fetch_assoc()) {
    $totalrating = $row_rating['totalrating'];
    $count++;
}

?>

        <select class="ratingshome" data-rating="<?php echo $totalrating; ?>">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        </div>
        <div class="col-6">
        <a class="bknw" href="view.php?bh_id=<?php echo $row['rental_id']; ?>">Book Now</a>
        </div>
        </div>
    </div>
  </div>
</div>

<?php
  }

?>


</div>

<center>
<ul class="pagination">
        <!-- <li><a href="?pageno=1"><i class="fa fa-fast-backward" aria-hidden="true"></i> First</a></li> -->
        <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1)."#recent"; } ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Prev</a>
        </li>
        <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1)."#recent"; } ?>">Next <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
        </li>
        <!-- <li><a href="?pageno=<?php //echo $total_pages; ?>">Last <i class="fa fa-fast-forward" aria-hidden="true"></i></a></li> -->
    </ul>
</center>


</div>
</div>

<script>
  // Disable Right Click
  document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
  });

  // Disable Ctrl+Shift+I, Ctrl+U, and F12
  document.addEventListener('keydown', function(e) {
    // Prevent F12 (Developer Tools)
    if (e.key === 'F12') {
      e.preventDefault();
    }
    // Prevent Ctrl+Shift+I (Open Developer Tools)
    if (e.ctrlKey && e.shiftKey && e.key === 'I') {
      e.preventDefault();
    }
    // Prevent Ctrl+Shift+J (Open Developer Tools Console)
    if (e.ctrlKey && e.shiftKey && e.key === 'J') {
      e.preventDefault();
    }
    // Prevent Ctrl+U (View Page Source)
    if (e.ctrlKey && e.key === 'U') {
      e.preventDefault();
    }
  });
</script>




<?php include('footer.php'); ?>