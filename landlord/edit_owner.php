<?php include('header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// Fetch owner details
$owner_id = $_GET['owner_id'];
$sql_edit = "SELECT * FROM register2 WHERE id='$owner_id'";
$result_edit = mysqli_query($dbconnection, $sql_edit);

while ($row_edit = $result_edit->fetch_assoc()) {
    $firstname = $row_edit['firstname'];
    $middlename = $row_edit['middlename'];
    $lastname = $row_edit['lastname'];
    $address = $row_edit['address'];
    $contact_number = $row_edit['contact_number'];
    $profile_photo = $row_edit['profile_photo'];
}
?>

<?php
if (isset($_POST["update"])) {
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];

    $new_profile_photo = $_FILES['profile_photo']['name'];
    $target = "../uploads/" . basename($new_profile_photo);

    if (!empty($new_profile_photo)) {
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target);
        $sql = "UPDATE register2 SET firstname='$firstname', middlename='$middlename', lastname='$lastname', address='$address', contact_number='$contact_number', profile_photo='$new_profile_photo' WHERE id='$owner_id'";
    } else {
        $sql = "UPDATE register2 SET firstname='$firstname', middlename='$middlename', lastname='$lastname', address='$address', contact_number='$contact_number' WHERE id='$owner_id'";
    }

    if ($dbconnection->query($sql) === TRUE) {
        echo '<script type="text/javascript">
            Swal.fire({
                icon: "success",
                title: "Updated",
                text: "Successfully Updated",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "dashboard.php";
                }
            });
        </script>';
    } else {
        echo '<script type="text/javascript">
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error updating record: ' . $dbconnection->error . '"
            });
        </script>';
    }
}
?>

<div class="row">
<div class="col-sm-2">
    <?php include('sidebar.php'); ?>
</div>

<div class="col-sm-9">
    <br />
    <h3>EDIT OWNER</h3>
    <br />
    <br />
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>First Name</label>
            <input name="firstname" type="text" class="form-control" value="<?php echo $firstname; ?>" required>
        </div>

        <div class="form-group">
            <label>Middle Name</label>
            <input name="middlename" type="text" class="form-control" value="<?php echo $middlename; ?>">
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input name="lastname" type="text" class="form-control" value="<?php echo $lastname; ?>" required>
        </div>

        <div class="form-group">
            <label>Address</label>
            <input name="address" type="text" class="form-control" value="<?php echo $address; ?>" required>
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input name="contact_number" type="text" class="form-control" value="<?php echo $contact_number; ?>" required>
        </div>

        <div class="form-group">
            <label>Profile Photo</label>
            <input type="file" name="profile_photo" class="form-control">
        </div>

        <button type="submit" name="update" class="btn btn-primary"><i class="fa fa-pencil-square" aria-hidden="true"></i> UPDATE</button>
    </form>
</div>
</div>

<?php include('footer.php'); ?>
