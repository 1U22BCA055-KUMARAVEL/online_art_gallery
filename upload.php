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

<style>
    body {
            background-image: url('images/image.png'); /* Ensure this file exists in the correct directory */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
.sidebar {
    width: 200px;
    background: #222;
    padding: 15px;
    position: fixed;
    height: 100%;
    color: white;
}
.sidebar a {
    display: block;
    color: white;
    padding: 10px;
    text-decoration: none;
}
.sidebar a:hover {
    background: #555;
}
form {
    margin-left: 220px;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px 0px #ccc;
    width: 50%;
}
input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
}
button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 4px;
}
button:hover {
    background-color: #0056b3;
}
</style>

<div class="sidebar">
    <a href="index.php">Home</a>
    <a href="gallery.php">Gallery</a>
    <a href="upload.php">Upload Art</a>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
</div>
<form method="POST" action="" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title" required><br>
    <input type="number" name="price" placeholder="Price" required><br>
    <input type="file" name="image" required><br>
    <button type="submit">Upload</button>
</form>