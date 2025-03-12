<?php
include('../config.php');

$password_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $password_error = "Password must be at least 8 characters long, include one uppercase letter, one number, and one special character.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $check_email_query = "SELECT email FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_email_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "<script>
                    alert('Email is already registered! Please log in.');
                    window.location.href = 'login.php';
                  </script>";
            exit();
        }

        $insert_query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Registration successful! Please log in.');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            echo "<script>alert('Error: Could not register. Try again later.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .register-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #5a67d8;
        }
        p {
            margin-top: 15px;
            font-size: 14px;
        }
        p a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="error-message"><?php echo $password_error; ?></div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>