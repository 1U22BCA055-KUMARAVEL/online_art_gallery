<?php
session_start();
include('config.php');
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
<header>
    <h1>Art Gallery</h1>
</header>
<main>
    <?php
    $query = "SELECT * FROM artwork ORDER BY artwork_id DESC";
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
</body>
</html>
