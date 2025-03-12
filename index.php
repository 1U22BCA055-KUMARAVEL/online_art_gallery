<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Art Gallery</title>
    <link rel="stylesheet" href="style.css">
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

.buy-btn {
    display: block;
    width: 100%;
    padding: 10px;
    background: #007bff;
    color: #fff;
    text-align: center;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}

.buy-btn:hover {
    background: #0056b3;
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
    <h2>Featured Artwork</h2>
    <div class="gallery">
        <?php
        include('config.php');
        $query = "SELECT * FROM artwork ORDER BY artwork_id DESC LIMIT 5";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='artwork'>";
            echo "<img src='uploads/" . $row['image'] . "' alt='" . $row['title'] . "'>";
            echo "<h3>" . $row['title'] . "</h3>";
            echo "<p>Price: ₹" . $row['price'] . "</p>";
            echo "</div>";
        }
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 Online Art Gallery</p>
</footer>

</body>
</html>
