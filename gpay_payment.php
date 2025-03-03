<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to proceed with payment'); window.location.href='login.php';</script>";
    exit;
}

$artwork_id = $_GET['artwork_id'];
$user_id = $_SESSION['user_id'];

// Fetch artwork details
$query = "SELECT * FROM artwork WHERE artwork_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $artwork_id);
$stmt->execute();
$result = $stmt->get_result();
$artwork = $result->fetch_assoc();

if ($artwork['user_id'] == $user_id) {
    echo "<script>alert('You cannot buy your own artwork!'); window.location.href='gallery.php';</script>";
    exit;
}

$price = $artwork['price'];
?>

<html>
<head>
    <title>Google Pay Payment</title>
    <script src="https://pay.google.com/gp/p/js/pay.js"></script>
    <script>
        const paymentRequest = {
            apiVersion: 2,
            apiVersionMinor: 0,
            allowedPaymentMethods: [{
                type: 'CARD',
                parameters: {
                    allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                    allowedCardNetworks: ['VISA', 'MASTERCARD']
                },
                tokenizationSpecification: {
                    type: 'PAYMENT_GATEWAY',
                    parameters: {
                        gateway: 'example',
                        gatewayMerchantId: 'your_merchant_id'
                    }
                }
            }],
            merchantInfo: {
                merchantId: 'your_merchant_id',
                merchantName: 'Art Gallery'
            },
            transactionInfo: {
                totalPriceStatus: 'FINAL',
                totalPrice: '<?php echo $price; ?>',
                currencyCode: 'INR' // Change dynamically based on user's location
            }
        };

        function onGooglePayLoaded() {
            const paymentsClient = new google.payments.api.PaymentsClient({ environment: 'TEST' });
            paymentsClient.isReadyToPay(paymentRequest).then(function(response) {
                if (response.result) {
                    const button = paymentsClient.createButton({
                        onClick: onGooglePaymentButtonClicked
                    });
                    document.getElementById('gpay-button').appendChild(button);
                }
            });
        }

        function onGooglePaymentButtonClicked() {
            const paymentsClient = new google.payments.api.PaymentsClient({ environment: 'TEST' });
            paymentsClient.loadPaymentData(paymentRequest).then(function(paymentData) {
                // Send transaction details to server
                fetch('process_gpay.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        artwork_id: '<?php echo $artwork_id; ?>',
                        amount: '<?php echo $price; ?>',
                        currency: 'INR'
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        alert("Payment successful!");
                        window.location.href = "gallery.php";
                    } else {
                        alert("Payment failed!");
                    }
                });
            }).catch(err => console.log(err));
        }
    </script>
</head>
<body onload="onGooglePayLoaded()">
    <h3>Buy <?php echo $artwork['title']; ?></h3>
    <p>Price: <?php echo $price; ?> INR</p>
    <div id="gpay-button"></div>
</body>
</html>
