<?php include('header.php'); ?>

<?php
if(!isset($_SESSION['login_user'])){
       header("location:../login.php");
       die();
     }

if (isset($_POST["delete"])) {
    $id = $_POST['rowid'];

    $sql = "DELETE FROM rental WHERE id=?";
    $stmt = $dbconnection->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire('Deleted!', 'Record Deleted Successfully', 'success')
                .then(() => {
                    window.location.href = window.location.pathname + window.location.search;
                });
        </script>";
    } else {
        echo "<script>
            Swal.fire('Error!', 'Error Deleting Record: " . addslashes($stmt->error) . "', 'error');
        </script>";
    }

    $stmt->close();
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
   
/* Table styles */
.table {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    font-size: 14px;
}

.table thead th {
    background: #007bff;
    color: #fff;
    text-align: center;
}

.table tbody td {
    vertical-align: middle;
    text-align: center;
    padding: 10px;
}
</style>

<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>
   
    <div class="main-content"> <br><br><br>
        <h3>My Boarding House </h3>
        
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Vacant</th>
                        <th>Occupied</th>
                        <th>View</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (isset($_GET['pageno'])) {
                    $pageno = $_GET['pageno'];
                } else {
                    $pageno = 1;
                }

                $no_of_records_per_page = 8;
                $offset = ($pageno - 1) * $no_of_records_per_page;

                $total_pages_sql = "SELECT COUNT(*) FROM register1";
                $result_pages = mysqli_query($dbconnection, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result_pages)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                $sql = "SELECT * FROM rental WHERE register1_id=? LIMIT ?, ?";
                $stmt = $dbconnection->prepare($sql);
                $stmt->bind_param("iii", $login_session, $offset, $no_of_records_per_page);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $rent_id = $row['rental_id'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td class="col-md-1">
                            <?php
                            $sql_book = "SELECT COUNT(*) FROM book WHERE bhouse_id=? AND register1_id=? AND status='Confirm'";
                            $stmt_book = $dbconnection->prepare($sql_book);
                            $stmt_book->bind_param("ii", $rent_id, $login_session);
                            $stmt_book->execute();
                            $result_book = $stmt_book->get_result();
                            $occupied = $result_book->fetch_array()[0];
                            $stmt_book->close();

                            $vacant_slots = $row['slots'] - $occupied;
                            echo htmlspecialchars($vacant_slots);
                            ?>
                        </td>
                        <td class="col-md-1"><?php echo htmlspecialchars($occupied); ?></td>
                        <td class="col-md-1"><a href="../view.php?bh_id=<?php echo htmlspecialchars($rent_id); ?>" class="btn btn-success"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                        <td class="col-md-1"><a href="edit.php?bh_id=<?php echo htmlspecialchars($rent_id); ?>" class="btn btn-warning"><i class="fa fa-pencil-square" aria-hidden="true"></i></a></td>
                        <td class="col-md-1">
                            <form action="" method="POST" class="delete-form">
                                <input type="hidden" name="rowid" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <input type="hidden" name="delete" value="1">
                                <button type="button" class="btn btn-danger delete-btn"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php 
                } 
                $stmt->close();
                ?>
                </tbody>
            </table>
        </div>

       

<?php include('footer.php'); ?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
