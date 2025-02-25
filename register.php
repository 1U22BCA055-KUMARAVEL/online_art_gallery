<?php
include('config.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    if (mysqli_query($conn, $query)) {
        echo "Registration successful. <a href='login.php'>Login</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<form method="POST" action="">
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form>
