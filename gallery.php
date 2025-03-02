
<?php
session_start();
include('config.php');
$query = "SELECT * FROM artwork ORDER BY artwork_id DESC";
$result = $conn->query($query);
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
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class='artwork'>
            <img src='uploads/<?= $row['image'] ?>' alt='<?= $row['title'] ?>'>
            <h3><?= $row['title'] ?></h3>
            <p>Price: $<?= $row['price'] ?></p>
        </div>
    <?php endwhile; ?>
</main>
</body>
</html>