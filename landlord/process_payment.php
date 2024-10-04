<?php
include('../connection.php'); // Your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['id'];
    $paid_amount = $_POST['paid_amount'];

    // Update the paid_amount in the book table
    $query = "UPDATE book SET paid_amount = paid_amount + ? WHERE id = ?";
    $stmt = $dbconnection->prepare($query);
    $stmt->bind_param("di", $paid_amount, $book_id);

    if ($stmt->execute()) {
        echo "<script>alert('Payment recorded successfully!'); window.location.href = 'your_page.php';</script>";
    } else {
        echo "<script>alert('Error recording payment.'); window.location.href = 'your_page.php';</script>";
    }

    // Close the prepared statement
    $stmt->close();
}
?>
