<?php
session_start();
include('config.php'); 

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('You must be logged in to upload an image!');
            window.location.href = 'login_module/login.php';
          </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];

    $target_dir = "uploads";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . "/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO artwork (title, price, image, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $title, $price, $image, $_SESSION['user_id']);

        if ($stmt->execute()) {
            echo "<script>alert('Artwork uploaded successfully.');</script>";
        } else {
            echo "<script>alert('Database Error: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error uploading file. Check folder permissions.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Artwork</title>
    
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

        /* Upload Form */
        .upload-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .upload-container h2 {
            text-align: center;
        }

        .upload-container input, .upload-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .upload-container input[type="file"] {
            padding: 5px;
        }

        .upload-container button {
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .upload-container button:hover {
            background: #0056b3;
        }

        /* Sidebar */
        .sidebar {
            background: #222;
            padding: 15px;
            position: fixed;
            left: 0;
            top: 0;
            width: 200px;
            height: 100%;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #444;
        }

        /* Footer */
        footer {
            text-align: center;
            background: #222;
            color: #fff;
            margin-top: 89px;
            width: 100%;
            position: absolute;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .upload-container {
                width: 90%;
            }
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



<div class="upload-container">
    <h2>Upload Artwork</h2>
    <form method="POST" action="" enctype="multipart/form-data" onsubmit="return checkLogin();">
        <input type="text" name="title" placeholder="Title" required>
        <input type="number" name="price" placeholder="Price" required>
        <input type="file" name="image" required>
        <button type="submit">Upload</button>
    </form>
</div>

<footer>
    <p>&copy; 2025 Online Art Gallery</p>
</footer>

<script>
function checkLogin() {
    var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    
    if (!isLoggedIn) {
        alert("You must be logged in to upload an image!");
        window.location.href = "login_module/login.php";
        return false;
    }
    return true;
}
</script>

</body>
</html>
