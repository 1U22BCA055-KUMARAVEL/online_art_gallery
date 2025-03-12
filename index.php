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
        /* Reset some default styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background-color: #f5f5f5;
    color: #333;
    line-height: 1.6;
}

/* Header Styles */
header {
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    color: white;
    padding: 20px;
    text-align: center;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

header h1 {
    font-size: 2rem;
}

nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
    margin-top: 10px;
}

nav ul li {
    margin: 0 15px;
}

nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 1.1rem;
    padding: 8px 15px;
    transition: background 0.3s ease-in-out;
}

nav ul li a:hover {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 5px;
}

/* Main Content */
main {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
    text-align: center;
}

h2 {
    font-size: 1.8rem;
    color: #444;
    margin-bottom: 20px;
}

/* Artwork Grid */
.artwork {
    background: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
}

.artwork img {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
    border-radius: 8px;
}

.artwork h3 {
    margin: 10px 0;
    font-size: 1.4rem;
}

.artwork p {
    color: #555;
    font-weight: bold;
}

.artwork:hover {
    transform: translateY(-5px);
}

/* Grid Layout */
.artwork-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}

/* Footer */
footer {
    background: #333;
    color: white;
    text-align: center;
    padding: 15px;
    margin-top: 30px;
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
                    <li><a href="login_module/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login_module/login.php">Login</a></li>
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
