<?php
session_start();
include('config.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    $query = "INSERT INTO artwork (title, price, image) VALUES ('$title', '$price', '$image')";
    if (mysqli_query($conn, $query)) {
        echo "Artwork uploaded successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<div class="sidebar">
    <a href="index.php">Home</a>
    <a href="gallery.php">Gallery</a>
</div>
<form method="POST" action="" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title" required><br>
    <input type="number" name="price" placeholder="Price" required><br>
    <input type="file" name="image" required><br>
    <button type="submit">Upload</button>
</form>

