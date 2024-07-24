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
    $new_password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirmpassword']) ? $_POST['confirmpassword'] : '';

    if (empty($new_password) || empty($confirm_password)) {
        echo "Password and confirm password are required.";
        exit();
    }

    if ($new_password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
        echo "Password must be at least 8 characters long and include at least one letter, one number, and one special character.";
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE users SET password='$hashed_password' WHERE id='$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Password updated successfully. You can now <a href='login.html'>login</a>.";
        unset($_SESSION['otp']);
        unset($_SESSION['otp_expiry']);
    } else {
        echo "Error updating password: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <div class="form-container">
            <h2>Reset Password</h2>
            <form action="reset_password.php" method="POST">
                <div class="field">
                    <input type="password" name="password" placeholder="New Password" required>
                </div>
                <div class="field">
                    <input type="password" name="confirmpassword" placeholder="Confirm Password" required>
                </div>
                <div class="field btn">
                    <input type="submit" value="Reset Password">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
