<?php
$servername = "localhost";
$username = "root";
$password = "globalwarn1705";
$database = "art_gallery";
$conn = new mysqli($servername, $username, $password, $database);
echo "Database connected successfully!";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>