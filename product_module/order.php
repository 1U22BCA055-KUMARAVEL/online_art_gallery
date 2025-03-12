<?php
session_start();
include('config.php');

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM orders WHERE user_id = ? AND payment_status = 'Completed'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h3>Your Orders</h3>";
while ($row = $result->fetch_assoc()) {
    echo "<p>Artwork ID: " . $row['artwork_id'] . "</p>";
    echo "<p>Paid: " . $row['total_amount'] . " " . $row['currency'] . " (≈ $" . $row['amount_in_usd'] . " USD)</p>";
    echo "<p>Status: " . $row['payment_status'] . "</p>";
}
?>
