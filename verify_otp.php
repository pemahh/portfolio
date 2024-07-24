<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Portfoliodb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = isset($_POST['otp']) ? $_POST['otp'] : '';

    if (empty($entered_otp)) {
        echo "OTP is required.";
        exit();
    }

    // Check if OTP is valid and not expired
    if ($_SESSION['otp'] == $entered_otp && time() < $_SESSION['otp_expiry']) {
        // OTP is valid
        echo "OTP verified successfully. Please reset your password.";
        header("Location: reset_password.php"); // Redirect to password reset page
        exit();
    } else {
        echo "Invalid or expired OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <div class="form-container">
            <h2>Verify OTP</h2>
            <form action="verify_otp.php" method="POST">
                <div class="field">
                    <input type="text" name="otp" placeholder="Enter OTP" required>
                </div>
                <div class="field btn">
                    <input type="submit" value="Verify OTP">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
