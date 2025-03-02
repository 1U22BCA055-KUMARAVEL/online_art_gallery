<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['artwork_id']) && isset($_POST['image'])) {
    $artwork_id = $_POST['artwork_id'];
    $image = $_POST['image'];
    $user_id = $_SESSION['user_id'];
    $is_admin = $_SESSION['is_admin']; // Get admin status

    // Check if artwork exists
    $check_stmt = $conn->prepare("SELECT user_id FROM artwork WHERE artwork_id = ?");
    $check_stmt->bind_param("i", $artwork_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    $check_stmt->bind_result($db_user_id);
    $check_stmt->fetch();

    if ($check_stmt->num_rows > 0) {
        if ($db_user_id == $user_id || $is_admin) {
            // Delete artwork from database
            $delete_stmt = $conn->prepare("DELETE FROM artwork WHERE artwork_id = ?");
            $delete_stmt->bind_param("i", $artwork_id);

            if ($delete_stmt->execute()) {
                // Delete the file from the uploads folder
                $file_path = "uploads/" . $image;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                echo "<script>
                        alert('Artwork deleted successfully!');
                        window.location.href = 'gallery.php';
                      </script>";
                exit(); // Stop execution after deletion
            } else {
                echo "<script>alert('Error deleting artwork. Try again!');</script>";
            }
        } else {
            echo "<script>alert('Unauthorized request!');</script>";
        }
    } else {
        echo "<script>alert('Artwork not found!');</script>";
    }
} else {
    echo "<script>alert('Invalid request!');</script>";
}
?>
