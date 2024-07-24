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
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';

    if (empty($email)) {
        echo "Email is required.";
        exit();
    }

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $otp = rand(100000, 999999); // Generate OTP
        $user_id = $user['id'];
        
        // Store OTP and expiration time in the session
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 300; // OTP expires in 5 minutes

        // Send OTP via email
        $to = $email;
        $subject = "Password Reset OTP";
        $message = "Your OTP for password reset is: $otp";
        $headers = "From: no-reply@example.com";

        if (mail($to, $subject, $message, $headers)) {
            echo "OTP sent to your email.";
            header("Location: verify_otp.php"); // Redirect to OTP verification page
            exit();
        } else {
            echo "Failed to send OTP. Please try again.";
        }
    } else {
        echo "No user found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <div class="form-container">
            <h2>Forgot Password</h2>
            <form action="forgot_password.php" method="POST">
                <div class="field">
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="field btn">
                    <input type="submit" value="Send OTP">
                </div>
            </form>
            <div class="signup-link">Remembered your password? <a href="login.html">Login here</a></div>
        </div>
    </div>
</body>
</html>
