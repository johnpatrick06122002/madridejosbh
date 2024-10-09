<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: register_step1.php");
    exit();
}

 

// Check if the form is submitted
if (isset($_POST['create_rental'])) {
    $title = $_POST['title'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $slots = $_POST['slots'];
    $map = $_POST['map'];
    $description = $_POST['description'];
    $monthly = $_POST['monthly'];
    $wifi = isset($_POST['free_wifi']) ? 'yes' : 'no';
    $water = isset($_POST['free_water']) ? 'yes' : 'no';
    $kuryente = isset($_POST['free_kuryente']) ? 'yes' : 'no';

    // Get the register1_id of the user
    $user_email = $_SESSION['email'];
    $stmt = $dbconnection->prepare("SELECT id FROM register1 WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $register1_id = $row['id'];

    // Photo uploads
    $photo = $_FILES['photo']['name'];
    $target_photo = "../uploadss/" . basename($photo);

    // Insert the rental information into the rental table
    $stmt2 = $dbconnection->prepare("INSERT INTO rental (register1_id, title, contact_number, address, slots, map, description, monthly, wifi, water, kuryente, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("issssssssss", $register1_id, $title, $contact_number, $address, $slots, $map, $description, $monthly, $wifi, $water, $kuryente, $photo);

    if ($stmt2->execute()) {
        // Move uploadsed file
        move_uploadsed_file($_FILES['photo']['tmp_name'], $target_photo);

        // Handle gallery uploads
        if (isset($_FILES['gallery'])) {
            $totalfiles = count($_FILES['gallery']['name']);
            for ($i = 0; $i < $totalfiles; $i++) {
                $gallery_file = $_FILES['gallery']['name'][$i];
                $gallery_target = "../uploadss/gallery/" . basename($gallery_file);
                if (move_uploadsed_file($_FILES["gallery"]["tmp_name"][$i], $gallery_target)) {
                    // Insert into gallery table
                    $insert = $dbconnection->prepare("INSERT INTO gallery (file_name, rental_id) VALUES (?, ?)");
                    $insert->bind_param("si", $gallery_file, $stmt2->insert_id);
                    $insert->execute();
                }
            }
        }

        // SweetAlert2 success message
        echo '<script type="text/javascript">
            Swal.fire({
                title: "Success!",
                text: "Rental property created successfully!",
                icon: "success",
                confirmButtonText: "OK"
            });
        </script>';
    } else {
        // SweetAlert2 error message
        echo '<script type="text/javascript">
            Swal.fire({
                title: "Error!",
                text: "Failed to create rental property. Please try again.",
                icon: "error",
                confirmButtonText: "OK"
            });
        </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Rental Property</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .rental-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="rental-form">
        <h2>Create Rental Property</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Property Title" required>
            <input type="text" name="contact_number" placeholder="Contact Number" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="number" name="slots" placeholder="Number of Slots" required>
            <input type="text" name="map" placeholder="Map Link" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="monthly" placeholder="Monthly Rent" required>
            <input type="file" name="photo" required>
            <div>
                <input type="checkbox" name="free_wifi"> Free Wifi
                <input type="checkbox" name="free_water"> Free Water
                <input type="checkbox" name="free_kuryente"> Free Kuryente
            </div>
            <input type="file" name="gallery[]" multiple>
            <button type="submit" name="create_rental">Create Rental</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
