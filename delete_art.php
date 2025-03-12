<?php
session_start();
include('../config.php');

$user_id = $_SESSION['user_id'] ?? null;
$is_admin = $_SESSION['is_admin'] ?? false; // Ensure admin status is checked

// **Optimized Query**
$query = "SELECT artwork_id, title, price, image, user_id FROM artwork";
$result = $conn->query($query);

// **Ensure Cache Directory Exists**
$cache_dir = "cache";
$cache_file = "$cache_dir/exchange_rate.json";
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0777, true);
}

// **Fetch Cached Exchange Rate**
$exchange_rate = 1; // Default if API fails
$currency = 'INR'; // Default currency
$api_url = "https://v6.exchangerate-api.com/v6/9678fa05d0512a06db00a83f/latest/INR";

if (file_exists($cache_file) && time() - filemtime($cache_file) < 3600) { // 1 hour cache
    $exchange_data = json_decode(file_get_contents($cache_file), true);
} else {
    $response = file_get_contents($api_url);
    $exchange_data = json_decode($response, true);
    if ($exchange_data && isset($exchange_data['conversion_rates']['USD'])) {
        file_put_contents($cache_file, json_encode($exchange_data));
    }
}

if ($exchange_data && isset($exchange_data['conversion_rates']['USD'])) {
    $exchange_rate = $exchange_data['conversion_rates']['USD']; // **Corrected exchange rate**
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="index.php">Home</a>
        <a href="upload.php">Upload Art</a>
    </div>

    <div class="gallery-container">
        <h2>Gallery</h2>
        <div class="gallery">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="artwork">
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" loading="lazy">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    
                    <?php 
                        // **Corrected Calculation**
                        $price_in_usd = round($row['price'] * $exchange_rate, 2);
                    ?>
                    <p>Price: <?php echo $row['price'] . " " . $currency; ?> (≈ $<?php echo $price_in_usd; ?> USD)</p>

                    <?php if ($user_id): ?>
                        <?php if ($row['user_id'] != $user_id): ?>
                            <a href="product_module/gpay_payment.php?artwork_id=<?php echo $row['artwork_id']; ?>&amount=<?php echo $row['price']; ?>&currency=<?php echo $currency; ?>">
                                <button class="buy-btn">Buy Now</button>
                            </a>
                        <?php else: ?>
                            <p><b>You cannot buy your own artwork</b></p>
                            <!-- Delete Button for the Owner or Admin -->
                            <form action="delete_art.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this artwork?');">
                                <input type="hidden" name="artwork_id" value="<?php echo $row['artwork_id']; ?>">
                                <input type="hidden" name="image" value="<?php echo htmlspecialchars($row['image']); ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>