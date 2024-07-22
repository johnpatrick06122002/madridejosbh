<?php include('connection.php'); ?>
<?php error_reporting(0); ?>
<?php include('landlord/session.php'); ?>

<?php 
if(isset($_POST["register"])) {

    $name = $_POST['name'];
    $Address = $_POST['Address'];
    $contact_number = "+63".$_POST['contact_number'];
    $facebook = $_POST['facebook'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_photo = $_FILES['profile_photo']['name'];
    $target = "uploads/".basename($profile_photo);

    $sql = "INSERT INTO landlords (name, email, password, Address, contact_number, facebook, profile_photo) VALUES ('$name', '$email', '$password', '$Address', '$contact_number', '$facebook', '$profile_photo')";

    if ($dbconnection->query($sql) === TRUE) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "success",
                        title: "Registration Successful",
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
              </script>';
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target);
    }
}

if(isset($_POST["login"])) {
    session_start();
    $myusername = $_POST['myemail'];
    $mypassword = $_POST['mypassword']; 
      
    $sql = "SELECT id FROM landlords WHERE email = '$myusername' and password = '$mypassword'";
    $result = mysqli_query($dbconnection,$sql);
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);
   
    if($count == 1) {
        $_SESSION['login_user'] = $myusername;
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "success",
                        title: "Login Successful",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "landlord/dashboard.php";
                    });
                });
              </script>';
    } else {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Login Failed",
                        text: "Username or Password is Incorrect",
                    });
                });
              </script>';
    }
}
?>

<?php $banner = array('banner.jpg', 'banner2.jpg', 'banner3.jpg'); ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="x-icon" href="bh.jpg">
	<title>MADRIE-BH</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href='src/fontawesome-stars.css' rel='stylesheet' type='text/css'>
  <!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

<style type="text/css">
 
.circular-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: url('bh.jpg') no-repeat center center;
            background-size: cover;
}
   
* {box-sizing: border-box;}

body {
  margin: 0;
}

.topnav {
  overflow: hidden;
  background-color: white;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 5;
  box-shadow: -2px 2px 10px #747474;

}

.topnav a {
  float: left;
  display: block;
  color: black;
  text-align: center;
  padding: 20px 21px;
  text-decoration: none;
  font-size: 17px;
}

.topnav a:hover {
  background-color: black;
  color: white;
}

.topnav a.active {
  background-color: white;
  color: black;
  border-radius: 10px;
}

.topnav .login-container {
  float: right;
}

.topnav input[type=text] {
  padding: 6px;
  margin-top: 8px;
  font-size: 17px;
  border: none;
  width:120px;
}

.topnav .login-container button {
  float: right;
  padding: 6px 10px;
  margin-top: 8px;
  margin-right: 16px;
  background-color: blue;
  color: white;
  font-size: 20px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}

.topnav .login-container button:hover {
  background-color: white;
  color:black;
}
.gallery img {
    object-fit: contain;
    height: 320px;
    width: auto;
    display: inline-block;
}
a.bknw {
    font-family: math;
    background: #59a14b;
    color: #fff;
    padding: 10px;
    float: right;
    border-radius: 3px;
    box-shadow: 2px 2px 2px #386430;
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
button.prev {
    position: absolute;
    top: 35%;
    left: 10%;
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    border-radius: 24px;
    width: 40px;
    height: 40px;
}

button.next {
    position: absolute;
    top: 35%;
    right: 10%;
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    border-radius: 24px;
    width: 40px;
    height: 40px;
}
form .br-theme-fontawesome-stars .br-widget a {
  font-size: 40px !important;
}
.message {
    background: #ffed6d;
    width: 320px;
    height: 200px;
    position: fixed;
    box-shadow: 2px 2px 2px #00000070;
    border: thin solid silver;
    border-radius: 10px;
    top: 50%;
    left: 50%;
    margin-top: -100px;
    margin-left: -150px;
    padding: 10px;
    z-index: 3;
}
a.closemessage {
    font-size: 38px;
    color: #c50e0e;
    position: absolute;
    top: 0;
    right: 7px;
}
.msgresult {
    margin-top: 30px;
    font-size: larger;
    font-weight: bold;
}

@media screen and (max-width: 600px) {
  .topnav .login-container {
    float: none;
  }
  .topnav a, .topnav input[type=text], .topnav .login-container button {
    float: none;
    display: block;
    text-align: left;
    width: 100%;
    margin: 0;
    padding: 14px;
  }
  .topnav input[type=text] {
    border: 1px solid #ccc;  
  }
}


.course_card {
  margin: 25px 10px;
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  word-wrap: break-word;
  background-color: #fff;
  background-clip: border-box;
  transition: 0.25s ease-in-out;
  border: thin solid #ff920a;
    box-shadow: 5px 4px 10px #9f9f9f;
}
.course_card_img {
  max-height: 100%;
  max-width: 100%;
}
.course_card_img img {
  height: 250px;
  width: 100%;
  transition: 0.25s all;
}
.course_card_img img:hover {
  transform: translateY(-3%);
}
.course_card_content {
  padding: 16px;
  height: 150px;
}
.course_card_content h3 {
  font-family: nunito sans;
  font-family: 18px;
}
.course_card_content p {
  font-family: nunito sans;
  text-align: justify;
}
.course_card_footer {
  padding: 10px 0px;
  margin: 16px;
}
.course_card_footer a {
  text-decoration: none;
  font-family: nunito sans;
/*  margin: 0 10px 0 0;*/
  text-transform: uppercase;
/*  color: #f96332;*/
/*  padding: 10px;*/
  font-size: 14px;
}
.course_card:hover {
  transform: scale(1.025);
  border-radius: 0.375rem;
  box-shadow: 0 0 2rem rgba(0, 0, 0, 0.25);
}
.course_card:hover .course_card_img img {
  border-top-left-radius: 0.375rem;
  border-top-right-radius: 0.375rem;
}


/*gallery*/

.wrap {
    overflow-x: scroll;
    position: relative;
}
.gallery {
    white-space: nowrap;
}


.main {
    width: 50%;
    margin: 50px auto;
}

/* Bootstrap 4 text input with search icon */

.has-search .form-control {
    padding-left: 2.375rem;
}

.has-search .form-control-feedback {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 2.375rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
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
  width: 160px;
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
}
</style>
</head>

<body>



<nav class="navbar navbar-dark navbar-expand-sm bg-dark fixed-top">
        <div class="container">
        <a href="index.php" class="navbar-brand">
        <i class="fas fa-blog"></i> &nbsp;
        <img class="logo" src="bh.jpg">MADRIDEJOS BH FINDER
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div id="navbarCollapse" class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="index.php" class="nav-link active">
                    Home
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link active">
                    About
                </a>
            </li>
            <li class="nav-item">
                <a href="" class="nav-link active">
                    Contact
                </a>
            </li>
            <?php if(empty($login_session)) { ?>
            <li class="nav-item">
    <a class="nav-link active" href="login.php"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</a>
</li>
     <li class="nav-item">
    <a class="nav-link active" href="admin/index.php"><i class="icon-copy fa fa-user-circle-o" aria-hidden="true"></i> Admin</a>
</li>
              
            <?php } else { ?>
              <li class="nav-item">
              <a class="nav-link active" href="landlord/dashboard.php"><i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="landlord/logout.php"><i class="fa fa-power-off" aria-hidden="true"></i> Logout</a>
            </li>
            <?php } ?>

        </ul>
        </div>
    </div>
</nav>


<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Register</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
  <form action="" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label for="name">Profile Picture</label>
    <input type="file" name="profile_photo" class="form-control" placeholder="Enter name">
  </div>    
   <div class="form-group">
    <label for="name">Full Name:</label>
    <input type="text" name="name" class="form-control" placeholder="Enter name">
  </div>
  <div class="form-group">
    <label for="email">Address:</label>
    <input type="text" name="Address" class="form-control" placeholder="Enter Address">
  </div>
  <div class="form-group">
      <div class="input-group-text">+63
      <input onkeypress='phnumber(event)' type="text" maxlength="10" minlength="10" name="contact_number" class="form-control" placeholder="Contact Number" required>
      </div>
  </div>
  <div class="form-group">
    <label for="email">Facebook Account:</label>
    <input type="url" name="facebook" class="form-control" placeholder="Enter Facebook Account URL">
  </div>
  <div class="form-group">
    <label for="email">Email address:</label>
    <input type="email" name="email" class="form-control" placeholder="Enter email">
  </div>
  
  <div class="form-group">
    <label for="pwd">Password:</label>
    <input type="password" name="password" class="form-control" placeholder="Enter password">
  </div>
  <div class="form-group form-check">
    <label class="form-check-label">
      <input class="form-check-input" type="checkbox"> Remember me
    </label>
  </div>
  <button type="submit" name="register" class="btn btn-primary">Register</button>
</form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>







  <!-- The Modal -->
<div class="modal" id="myLogin">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Login</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">


<form action="" method="post">
  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input name="myemail" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input name="mypassword" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>

  <button type="submit" name="login" class="btn btn-primary">Login</button>
</form>


  </div>
      </div>

      <!-- Modal footer -->
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div> -->

    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
 



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