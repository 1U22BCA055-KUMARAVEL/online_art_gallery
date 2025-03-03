<?php
session_start();
include('config.php');

$user_id = $_SESSION['user_id'] ?? null; // Check if user is logged in

$query = "SELECT * FROM artwork";
$result = $conn->query($query);

// Fetch latest exchange rates (convert any currency to USD)
$api_url = "https://v6.exchangerate-api.com/v6/a996cca7febe7893cfe75586/latest/USD";
$response = file_get_contents($api_url);
$exchange_data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-image: url('images/image.png'); background-size: cover; background-position: center; background-attachment: fixed; display: flex; }
        
        .sidebar { width: 250px; background: #222; padding: 20px; height: 100vh; position: fixed; color: white; }
        .sidebar h2 { text-align: center; margin-bottom: 20px; }
        .sidebar a { display: block; color: white; padding: 12px; text-decoration: none; border-radius: 5px; margin-bottom: 10px; text-align: center; background: #333; }
        .sidebar a:hover { background: #555; }

        .gallery-container { margin-left: 270px; padding: 20px; width: calc(100% - 270px); }
        h2 { text-align: center; margin-bottom: 20px; color: white; background: rgba(0, 0, 0, 0.7); padding: 10px; border-radius: 5px; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; justify-content: center; }
        .artwork { background: white; padding: 15px; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); text-align: center; }
        .artwork img { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; }
        .artwork h3 { margin: 10px 0; font-size: 18px; }
        .artwork p { color: #555; font-size: 16px; }
        .buy-btn { background-color: #28a745; color: white; padding: 8px 12px; border: none; cursor: pointer; border-radius: 5px; margin-top: 10px; transition: background 0.3s; }
        .buy-btn:hover { background-color: #218838; }
        .delete-btn { background-color: red; color: white; padding: 8px 12px; border: none; cursor: pointer; border-radius: 5px; margin-top: 10px; transition: background 0.3s; }
        .delete-btn:hover { background-color: darkred; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; text-align: center; position: relative; padding: 10px; }
            .gallery-container { margin-left: 0; width: 100%; }
            .gallery { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
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
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="artwork">
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    
                    <?php 
                        // Convert price to USD dynamically
                        $currency = 'INR'; // Change this dynamically based on user's location (fetch from DB)
                        $exchange_rate = isset($exchange_data['rates'][$currency]) ? $exchange_data['rates'][$currency] : 1;
                        $price_in_usd = round($row['price'] / $exchange_rate, 2);
                    ?>
                    
                    <p>Price: <?php echo $row['price'] . " " . $currency; ?> (≈ $<?php echo $price_in_usd; ?> USD)</p>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                            <a href="gpay_payment.php?artwork_id=<?php echo $row['artwork_id']; ?>&amount=<?php echo $row['price']; ?>&currency=<?php echo $currency; ?>">
                                <button class="buy-btn">Buy with Google Pay</button>
                            </a>
                        <?php else: ?>
                            <p><b>You cannot buy your own artwork</b></p>
                        <?php endif; ?>

                        <?php if ($_SESSION['is_admin'] || $_SESSION['user_id'] == $row['user_id']): ?>
                            <form method="POST" action="delete_art.php" onsubmit="return confirm('Are you sure you want to delete this artwork?');">
                                <input type="hidden" name="artwork_id" value="<?php echo $row['artwork_id']; ?>">
                                <input type="hidden" name="image" value="<?php echo $row['image']; ?>">
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
