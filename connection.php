<?php
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "Portfoliodb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmpassword = isset($_POST['confirmpassword']) ? $_POST['confirmpassword'] : '';

    if (empty($username)) {
        die("Username is required.");
    }
    if (empty($email)) {
        die("Email is required.");
    }
    if (empty($phone)) {
        die("Phone number is required.");
    }
    if (empty($password)) {
        die("Password is required.");
    }
    if (empty($confirmpassword)) {
        die("Confirm password is required.");
    }

    // Validate email domain
    $email_domain = substr(strrchr($email, "@"), 1);
    if ($email_domain !== "gmail.com") {
        die("Only Gmail addresses are allowed.");
    }

    // Validate password
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        die("Password must be at least 8 characters long and include at least one letter, one number, and one special character.");
    }

    // Confirm passwords match
    if ($password !== $confirmpassword) {
        die("Passwords do not match.");
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

     // Check if email is already in use
     $check_email_sql = "SELECT * FROM users WHERE email = '$email'";
     $check_email_result = $conn->query($check_email_sql);
     if ($check_email_result->num_rows > 0) {
         die("Email address is already in use.");
     }
     
    $sql = "INSERT INTO users (username, email, phone, password) VALUES ('$username', '$email', '$phone', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Successfully registered! Redirecting to login page...');
                window.location.href = 'login.html';
              </script>";
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
