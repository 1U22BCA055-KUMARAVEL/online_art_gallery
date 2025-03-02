<?php
session_start();
include('config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Art Gallery</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('images/image.png'); /* Ensure this file exists in the correct directory */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to the Online Art Gallery</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="upload.php">Upload Art</a></li>
                <?php if(isset($_SESSION['email'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
                
            </ul>
        </nav>
    </header>
    <main>
        <h2>Featured Artwork</h2>
        <?php
        $query = "SELECT * FROM artwork ORDER BY artwork_id DESC LIMIT 5";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='artwork'>";
            echo "<img src='uploads/" . $row['image'] . "' alt='" . $row['title'] . "'>";
            echo "<h3>" . $row['title'] . "</h3>";
            echo "<p>Price: $" . $row['price'] . "</p>";
            echo "</div>";
        }
        ?>
    </main>
    <footer>
        <p>&copy; 2025 Online Art Gallery</p>
    </footer>
</body>
</html>
