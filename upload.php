<?php
session_start();
include('config.php'); // Ensure database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('You must be logged in to upload an image!');
            window.location.href = 'login.php';
          </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];

    // Ensure the 'uploads/' folder exists
    $target_dir = "uploads";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . "/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO artwork (title, price, image, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $title, $price, $image, $_SESSION['user_id']);

        if ($stmt->execute()) {
            echo "Artwork uploaded successfully.";
        } else {
            echo "Database Error: " . $stmt->error;
        }
    } else {
        echo "Error uploading file. Check folder permissions.";
    }
}
?>


<style>
body {
    background-image: url('images/image.png');
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

</div>

<script>
function checkLogin() {
    var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    
    if (!isLoggedIn) {
        alert("You must be logged in to upload an image!");
        window.location.href = "login.php";
        return false;
    }
    return true;
}
</script>

<form method="POST" action="" enctype="multipart/form-data" onsubmit="return checkLogin();">
    <input type="text" name="title" placeholder="Title" required><br>
    <input type="number" name="price" placeholder="Price" required><br>
    <input type="file" name="image" required><br>
    <button type="submit">Upload</button>
</form>
