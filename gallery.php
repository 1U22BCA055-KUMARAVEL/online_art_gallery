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
    <style>
        /* General Styles */
        body {
            background-image: url('images/image.png');
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            color: #333;
        }

        header {
            background: #222;
            color: #fff;
            padding: 15px 20px;
            text-align: center;
        }

        nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            transition: 0.3s;
        }

        nav ul li a:hover {
            color: #ffc107;
        }

        /* Gallery Grid */
        .gallery-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 10px;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .artwork {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .artwork:hover {
            transform: translateY(-5px);
        }

        .artwork img {
            width: 100%;
            border-radius: 10px;
            height: 200px;
            object-fit: cover;
        }

        .artwork h3 {
            font-size: 20px;
            margin: 10px 0;
        }

        .artwork p {
            font-size: 16px;
            color: #666;
        }

        /* Footer */
        footer {
            text-align: center;
            background: #222;
            color: #fff;
            padding: 10px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<header>
    <h1>Online Art Gallery</h1>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="upload.php">Upload Art</a></li>
            <?php if(isset($_SESSION['email'])): ?>
                <li><a href="login_module/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login_module/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main class="gallery-container">
    <h2>Gallery</h2>
    <div class="gallery">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="artwork">
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" loading="lazy">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                
                <?php 
                    $price_in_usd = round($row['price'] * $exchange_rate, 2);
                ?>
                <p>Price: ₹<?php echo $row['price']; ?> (≈ $<?php echo $price_in_usd; ?> USD)</p>

                <?php if ($user_id): ?>
                    <?php if ($row['user_id'] != $user_id): ?>
                        <a href="product_module/gpay_payment.php?artwork_id=<?php echo $row['artwork_id']; ?>&amount=<?php echo $row['price']; ?>&currency=<?php echo $currency; ?>">
                            <button class="buy-btn">Buy Now</button>
                        </a>
                    <?php else: ?>
                        <p><b>You cannot buy your own artwork</b></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 Online Art Gallery</p>
</footer>

</body>
</html>