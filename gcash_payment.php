<?php
// gcash_payment.php
$amount = $_GET['amount']; // Amount to charge
$gcash_number = $_GET['gcash_number']; // User's GCash number

// Here, you would typically integrate with GCash's payment API or use a method to prompt the GCash app
// For demo purposes, let's assume we are just displaying a message
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCash Payment</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        // Simulating a successful payment process
        Swal.fire({
            icon: "info",
            title: "GCash Payment",
            text: "You will now be redirected to the GCash app to complete your payment of ₱<?php echo $amount; ?>.",
        }).then(() => {
            // Here, you would actually integrate with GCash's API or redirect the user to a payment link
            // For now, just simulating a redirection
            window.location.href = "https://example.com/gcash/payment?amount=<?php echo $amount; ?>&number=<?php echo $gcash_number; ?>"; // Change to the actual payment link
        });
    </script>
</body>
</html>
