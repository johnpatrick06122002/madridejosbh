<?php
session_start(); // Start session to check login status
include('connection.php');
error_reporting(0);
include('landlord/session.php');

// Determine if the user is logged in based on the session variable
$login_session = $_SESSION['admin_loggedin'] ?? null;

// Determine the user role based on session variables
$is_admin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;
$is_landlord = isset($_SESSION['login_user']) && !empty($_SESSION['login_user']);

?>

<?php $banner = array('banner.jpg', 'banner2.jpg', 'banner3.jpg'); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MADRIE-BH</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="x-icon" href="bhh.jpg">
     <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
 
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- FontAwesome Stars CSS -->
    <link href='src/fontawesome-stars.css' rel='stylesheet' type='text/css'>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
 
<style type="text/css">

* {box-sizing: border-box;}
 
.gallery img {
    object-fit: contain;
    height: 320px;
    width: auto;
    display: inline-block;
}

img.logo {
    width: 35px;
    height: 35px;
    border-radius: 100px;
    margin-right: 10px;
}
.bigbanner {
    height: 450px;
    background-image: url(<?php echo $banner[array_rand($banner)]; ?>);
}

h2.tagline {
    font-size: 45px;
    text-align: center;
    margin-top: 20px;
    color: #fff;
    text-shadow: 1px 1px black;
    animation-name: fade-in;
    animation-duration: 5s;
}
@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
.navbar {
  background-color: #343a408f !important;
}
div#recent {
    margin-top: -80px;
}

.wrap::-webkit-scrollbar {
    display: none;
}
 
form .br-theme-fontawesome-stars .br-widget a {
  font-size: 40px !important;
}
 
/* For large screens (992px and above) */
@media (min-width: 992px) {
    .navbar-expand-lg .navbar-collapse {
        position: absolute; /* Adjusted to 'absolute' for better control */
        display: flex !important;
        -ms-flex-preferred-size: auto;
        flex-basis: auto;
        margin-left: 58%;
        right: 50px; /* Align navbar to the right */
    }

    /* Underline effect for navbar items on hover */
    .navbar-expand-lg .navbar-collapse .nav-item:hover .nav-link {
        text-decoration: underline; /* Add underline on hover */
        color: #f39c12; /* Ensure the text remains white */
    }
}
.gallery {
    white-space: nowrap;
}


.main {
    width: 50%;
    margin: 50px auto;
}

div#searchbox {
  max-width: 800px;
  width: 80%;
  margin-top: 50px;
}
div#searchbox input.form-control {
    font-size: 25px;
    text-align: center;
}

div#searchbox button.btn.btn-secondary {
    width: 60px;
    background: #59a14b;
    border: green solid thin;
}

.pagination {
  width: 150px;
}
ul.pagination li {
    background: #04AA6D;
    padding: 10px;
    margin: 5px;
    border: thin solid silver;
}
ul.pagination li a {
  color: #fff;
}
ul.pagination li.disabled {
  background: #adadad;
}
ul.pagination li:last-child {
  float: right;
}.navbar {
    background-color: #343a408f !important;
    padding: 0.5rem 1rem;
}

.navbar-brand {
    display: flex;
    align-items: center;
    padding: 0;
}

.navbar-brand img.logo {
    margin-right: 10px;
}

.navbar-dark .navbar-toggler {
    border-color: rgba(255,255,255,.5);
    padding: .25rem .75rem;
}

.navbar-dark .navbar-toggler:focus {
    outline: none;
    box-shadow: none;
}

 

/* For smaller screens (max-width: 991px) */
@media (max-width: 991.98px) {
    .navbar-collapse {
        background-color: #343a408f;
        padding: 1rem;
        margin-top: 0.5rem;
    }

    .navbar-nav .nav-link {
        padding: 0.5rem 0;
    }

    /* Optionally adjust nav-link for smaller screens */
    .navbar-nav .nav-link {
        font-size: 16px; /* Adjust font size for readability */
    }
}
   .navbar {
      position: fixed;
      width: 100%;
      height: 10%;
      top: 0;
      left: 0;
      z-index: 1030; /* Keeps navbar on top */
      
    }

    /* Ensure page content is not hidden under the fixed navbar */
    .content {
      margin-top: 70px; /* Adjust based on navbar height */
    }

    .navbar-collapse {
      transition: height 0.3s ease;
    }

   /* Enhance the navbar brand text */
.navbar-dark .navbar-brand {
    color: white; /* Retaining your brand color */
    font-family: 'Roboto', sans-serif; /* Example: Using a clean sans-serif font (Google Font) */
    font-size: 1.75rem; /* Larger font size */
    font-weight: 700; /* Bold weight for emphasis */
    letter-spacing: 1px; /* Slightly increased letter spacing for a more refined look */
    text-transform: uppercase; /* Capitalize all letters for a more uniform appearance */
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); /* Add a subtle shadow for depth */
    transition: all 0.3s ease; /* Smooth transition for hover effects */
}

/* Optional: Hover effect for brand */
.navbar-dark .navbar-brand:hover {
    color: #f39c12; /* Change color on hover for interaction */
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.4); /* Stronger shadow on hover for emphasis */
}

</style>
</head>


<!-- Updated Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="">Madridejos Boarding House Finder</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">Home</a>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link active">About</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link active">Contact</a>
                </li>
                <?php if(empty($login_session)) { ?>
                <li class="nav-item">
                    <a class="nav-link active" href="login.php">
                        <i class="fa fa-sign-in" aria-hidden="true"></i> Sign In
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="register_step1.php">
                        <i class="icon-copy fa fa-user-circle-o" aria-hidden="true"></i> Sign Up
                    </a>
                </li>
                  <?php } else { ?>
                <!-- Show Dashboard link based on role -->
                <li class="nav-item">
                    <?php if ($is_admin) { ?>
                        <a class="nav-link active" href="admin/dashboard.php">
                            <i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard
                        </a>
                    <?php } elseif ($is_landlord) { ?>
                        <a class="nav-link active" href="landlord/dashboard.php">
                            <i class="fa fa-tachometer" aria-hidden="true"></i> Landlord Dashboard
                        </a>
                    <?php } ?>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="logout.php">
                        <i class="fa fa-sign-out" aria-hidden="true"></i> Sign Out
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
<!-- header.php -->


  </div>
      </div>

      <!-- Modal footer -->
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div> -->

    </div>
  </div>
   <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
      $('.navbar-nav .nav-link').on('click', function () {
        $('.navbar-collapse').collapse('hide');
      });
    });
  </script>
<script>
$(document).ready(function() {
    // Handle click event on navbar-toggler
    $('.navbar-toggler').click(function(e) {
        e.preventDefault();
        $('#navbarCollapse').collapse('toggle');
    });

    // Handle click event on nav links in mobile view
    $('.navbar-nav .nav-link').click(function() {
        if ($('.navbar-toggler').is(':visible')) {
            $('.navbar-collapse').collapse('hide');
        }
    });

    // Handle window resize
    $(window).resize(function() {
        if ($(window).width() > 991) {
            $('.navbar-collapse').removeClass('show');
        }
    });
});
</script>



<script>
function phnumber(evt) {
  var theEvent = evt || window.event;

  // Handle paste
  if (theEvent.type === 'paste') {
      key = event.clipboardData.getData('text/plain');
  } else {
  // Handle key press
      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
  }
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}
</script>