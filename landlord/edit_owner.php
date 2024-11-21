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

/* Table container */
.table-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    margin-bottom: 20px;
    overflow: hidden;
}

/* Table styles */
.table-responsive {
    margin: 0;
    padding: 0;
    width: 100%;
}

.table {
    margin-bottom: 0;
    width: 100%;
}

.table th {
    background: #f8f9fc;
    font-weight: 600;
    padding: 12px 15px;
    white-space: nowrap;
}

.table td {
    padding: 12px 15px;
    vertical-align: middle;
}

/* Button styles */
.action-buttons {
    display: flex;
    gap: 5px;
}

.btn {
    padding: 6px 12px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
}

.btn i {
    font-size: 14px;
}

 

/* Header styles */
h3 {
    margin: 0 0 20px 0;
    color: #5a5c69;
    font-weight: 500;
    font-size: 1.75rem;
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

    .table th, .table td {
        padding: 8px;
        font-size: 14px;
    }

    .btn {
        padding: 4px 8px;
        min-width: 30px;
    }

     

    h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .action-buttons {
        flex-direction: column;
        gap: 3px;
    }
}
</style>
<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>
   
    <div class="main-content"> <br><br><br>
           
            <h3>EDIT OWNER</h3>
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
</div>

<?php include('footer.php'); ?>
