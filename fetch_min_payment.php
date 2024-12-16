<?php
include('connection.php');
header('Content-Type: application/json');

$rental_id = isset($_GET['rental_id']) ? intval($_GET['rental_id']) : 0;
$response = ['min_payment' => 0];

if ($rental_id) {
    $sql = "SELECT GREATEST(IFNULL(downpayment_amount, 0), IFNULL(installment_amount, 0)) AS min_payment FROM rental WHERE rental_id = ?";
    $stmt = $dbconnection->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $rental_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $response['min_payment'] = floatval($result['min_payment']);
    }
}

echo json_encode($response);
?>
