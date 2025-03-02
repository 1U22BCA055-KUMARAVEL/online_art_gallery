<?php
$servername = "localhost";
$username = "root";
$password = "globalwarn1705";
$database = "art_gallery";
$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
