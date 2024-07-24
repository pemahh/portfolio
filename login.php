<?php
session_start();
$servername = "127.0.0.1";
$username = "root"; // replace with your database username
$password = "123456789"; // replace with your database password
$dbname = "Portfoliodb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize login attempts session variable
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Initialize lockout time session variable
if (!isset($_SESSION['lockout_time'])) {
    $_SESSION['lockout_time'] = 0;
}

$current_time = time();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is locked out
    if ($_SESSION['login_attempts'] >= 3 && $current_time < $_SESSION['lockout_time']) {
        $remaining_time = $_SESSION['lockout_time'] - $current_time;
        echo "<script>
            sessionStorage.setItem('lockout_end_time', " . ($_SESSION['lockout_time']) . ");
            alert('You have been locked out for 20 seconds due to multiple failed login attempts. Please try again after {$remaining_time} seconds.');
            window.location.href = 'login.html';
        </script>";
        exit();
    }

    // Sanitize and validate input
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? $_POST['remember'] : '';

    if (empty($email)) {
        die("Email is required.");
    }
    if (empty($password)) {
        die("Password is required.");
    }

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password is correct, reset login attempts
            $_SESSION['login_attempts'] = 0;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            if ($remember) {
                echo "<script>setCookie('remember_me', 'true', 10);</script>";
            }

            header("Location: dashboard.php"); // Redirect to a dashboard page
            exit();
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= 3) {
                $_SESSION['lockout_time'] = $current_time + 20; // 20 seconds lockout
                echo "<script>
                    sessionStorage.setItem('lockout_end_time', " . ($_SESSION['lockout_time']) . ");
                    alert('You have been locked out for 20 seconds due to multiple failed login attempts.');
                    window.location.href = 'login.html';
                </script>";
                exit();
            } else {
                die("Invalid password. You have " . (3 - $_SESSION['login_attempts']) . " attempt(s) left.");
            }
        }
    } else {
        die("No user found with that email address.");
    }
}

// Handle logout
if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header("Location: login.html");
    exit();
}
?>
