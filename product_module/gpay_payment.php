<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to proceed with payment'); window.location.href='login.php';</script>";
    exit;
}

$artwork_id = $_GET['artwork_id'];
$amount = $_GET['amount'];
$currency = $_GET['currency'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Pay Payment</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { display: flex; align-items: center; justify-content: center; height: 100vh; background: #f9f9f9; }
        
        .container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            width: 350px;
        }

        h2 { color: #333; margin-bottom: 15px; }
        p { font-size: 16px; color: #555; }
        
        .qr-container {
            margin: 20px auto;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
            display: inline-block;
        }
        
        .qr-container img {
            width: 150px;
            height: 150px;
            border-radius: 8px;
        }

        .pay-btn {
            display: inline-block;
            width: 100%;
            background: #007bff;
            color: white;
            font-size: 16px;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
            transition: background 0.3s;
        }

        .pay-btn:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Buy Artwork</h2>
        <p><strong>Price:</strong> <?php echo $amount . " " . $currency; ?></p>
        
        <p>Scan this QR code to pay:</p>
        <div class="qr-container">
        <img src="../images/gpay.png">
        </div>

        <button class="pay-btn" onclick="confirmPayment()">Confirm Payment</button>
    </div>

    <script>
        function confirmPayment() {
            let txn_id = prompt("Enter UPI Transaction ID:");
            if (!txn_id) {
                alert("Transaction ID is required!");
                return;
            }

            let address = prompt("Enter your delivery address:");
            if (!address) {
                alert("Address is required!");
                return;
            }

            let artworkId = <?php echo $artwork_id; ?>;
            let amount = <?php echo $amount; ?>;
            let currency = "<?php echo $currency; ?>";

            fetch("process_payment.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    artwork_id: artworkId,
                    amount: amount,
                    currency: currency,
                    address: address,
                    txn_id: txn_id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Payment Confirmed! Order Placed.");
                    window.location.href = "online_art_gallery/gallery.php";
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error("Error:", err));
        }
    </script>

</body>
</html>





