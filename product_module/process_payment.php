<?php
session_start();
include('../config.php');

$data = json_decode(file_get_contents("php://input"), true);

// Debugging: Check if JSON data is received
if (!$data) {
    echo json_encode(["success" => false, "message" => "No JSON received!", "raw" => file_get_contents("php://input")]);
    exit;
}

// **Check if all required fields are received**
if (!isset($data['artwork_id']) || !isset($data['txn_id']) || !isset($data['address'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request!']);
    exit;
}

$artwork_id = $data['artwork_id'];
$user_id = $_SESSION['user_id'];
$amount = $data['amount'];
$currency = $data['currency'];
$address = $data['address'];
$txn_id = $data['txn_id'];

// **Check if Payment is Already Processed**
$checkTxn = $conn->prepare("SELECT txn_id FROM orders WHERE txn_id = ?");
$checkTxn->bind_param("s", $txn_id);
$checkTxn->execute();
$checkTxn->store_result();

if ($checkTxn->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Payment already processed!']);
    exit;
}

// **Fetch Cached Exchange Rate for Faster Processing**
$cache_file = "cache/exchange_rate.json";
$exchange_rate = 1;
if (file_exists($cache_file) && time() - filemtime($cache_file) < 3600) {
    $exchange_data = json_decode(file_get_contents($cache_file), true);
} else {
    $api_url = "https://v6.exchangerate-api.com/v6/a996cca7febe7893cfe75586/latest/$currency";
    $exchange_data = json_decode(file_get_contents($api_url), true);
    if ($exchange_data && isset($exchange_data['conversion_rates']['USD'])) {
        file_put_contents($cache_file, json_encode($exchange_data));
    }
}
if ($exchange_data && isset($exchange_data['conversion_rates']['USD'])) {
    $exchange_rate = $exchange_data['conversion_rates']['USD'];
}
$amount_in_usd = round($amount * $exchange_rate, 2);

// **Insert Verified Order Into Database**
$stmt = $conn->prepare("INSERT INTO orders (user_id, artwork_id, total_amount, currency, amount_in_usd, payment_status, address, txn_id) VALUES (?, ?, ?, ?, ?, 'Completed', ?, ?)");
$stmt->bind_param("iidssss", $user_id, $artwork_id, $amount, $currency, $amount_in_usd, $address, $txn_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>





