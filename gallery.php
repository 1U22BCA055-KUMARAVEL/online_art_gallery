<?php
session_start();
include('config.php');

$user_id = $_SESSION['user_id'] ?? null;

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
$api_url = "https://v6.exchangerate-api.com/v6/YOUR_API_KEY/latest/INR";

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
    <style>
        body { font-family: Arial, sans-serif; display: flex; background: #f4f4f4; }
        
        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background: #222;
            padding: 20px;
            height: 100vh;
            position: fixed;
            color: white;
        }
        .sidebar h2 { text-align: center; margin-bottom: 20px; }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
            background: #333;
        }
        .sidebar a:hover { background: #555; }

        /* Gallery Section */
        .gallery-container { margin-left: 270px; padding: 20px; width: calc(100% - 270px); }
        .gallery { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .artwork { background: white; padding: 15px; border-radius: 8px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.2); width: 250px; }
        .artwork img { width: 100%; height: 180px; object-fit: cover; border-radius: 8px; }
        .buy-btn { background-color: #28a745; color: white; padding: 8px 12px; border: none; cursor: pointer; border-radius: 5px; }
        .buy-btn:hover { background-color: #218838; }
        .delete-btn { background-color: red; color: white; padding: 8px 12px; border: none; cursor: pointer; border-radius: 5px; }
        .delete-btn:hover { background-color: darkred; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; text-align: center; position: relative; padding: 10px; }
            .gallery-container { margin-left: 0; width: 100%; }
        }
    </style>
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
                            <a href="gpay_payment.php?artwork_id=<?php echo $row['artwork_id']; ?>&amount=<?php echo $row['price']; ?>&currency=<?php echo $currency; ?>">
                                <button class="buy-btn">Buy Now</button>
                            </a>
                        <?php else: ?>
                            <p><b>You cannot buy your own artwork</b></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
