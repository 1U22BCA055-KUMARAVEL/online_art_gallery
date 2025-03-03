<?php
session_start();
include('config.php');

$data = json_decode(file_get_contents('php://input'), true);
$artwork_id = $data['artwork_id'];
$user_id = $_SESSION['user_id'];
$amount = $data['amount'];
$currency = $data['currency'];

// API to get exchange rate
$api_url = "https://v6.exchangerate-api.com/v6/a996cca7febe7893cfe75586/latest/$currency";
$response = file_get_contents($api_url);
$exchange_data = json_decode($response, true);

if (!$exchange_data || !isset($exchange_data['rates']['USD'])) {
    echo json_encode(['success' => false, 'message' => 'Currency conversion failed']);
    exit;
}

$exchange_rate = $exchange_data['rates']['USD'];
$amount_in_usd = $amount * $exchange_rate;

// Insert into database
$stmt = $conn->prepare("INSERT INTO orders (user_id, artwork_id, total_amount, currency, amount_in_usd, payment_status) VALUES (?, ?, ?, ?, ?, 'Completed')");
$stmt->bind_param("iidss", $user_id, $artwork_id, $amount, $currency, $amount_in_usd);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
