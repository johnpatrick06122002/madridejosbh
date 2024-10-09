<?php 

//Database Connection
$conn = mysqli_connect('localhost', 'root', '', 'test');
//Check for connection error
if($conn->connect_error){
  die("Error in DB connection: ".$conn->connect_errno." : ".$conn->connect_error);    
}

if(isset($_POST['submit'])){
 // Count total uploadsed files
 $totalfiles = count($_FILES['file']['name']);

 // Looping over all files
 for($i=0;$i<$totalfiles;$i++){
 $filename = $_FILES['file']['name'][$i];
 
// uploads files and store in database
if(move_uploadsed_file($_FILES["file"]["tmp_name"][$i],'../test/'.$filename)){
        // Image db insert sql
        $insert = "INSERT into files(file_name,uploadsed_on,status) values('$filename',now(),1)";
        if(mysqli_query($conn, $insert)){
          echo 'Data inserted successfully';
        }
        else{
          echo 'Error: '.mysqli_error($conn);
        }
    }else{
        echo 'Error in uploadsing file - '.$_FILES['file']['name'][$i].'<br/>';
    }
 
 }
} 
?>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h1>Select Files to uploads</h1>
    <form method='post' action='#' enctype='multipart/form-data'>
    <div class="form-group">
     <input type="file" name="file[]" id="file" multiple>
    </div> 
    <div class="form-group"> 
     <input type='submit' name='submit' value='uploads' class="btn btn-primary">
    </div> 
    </form>
</div>  
</body>
</html>