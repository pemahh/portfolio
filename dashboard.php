<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "Portfoliodb";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details from database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}

// Handle image upload and removal
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Remove profile picture
    if (isset($_POST['remove_picture'])) {
        $sql = "UPDATE users SET profile_picture = NULL WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            $user['profile_picture'] = NULL;
            header("Location: dashboard.php"); // Refresh page
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }

    // Handle image upload
    if (isset($_FILES["profile_picture"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;
        $maxFileSize = 2 * 1024 * 1024; // 2 MB

        // Check file size
        if ($_FILES["profile_picture"]["size"] > $maxFileSize) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow only certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
            echo "Sorry, only JPG, JPEG, PNG files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                // Save file path to database
                $file_path = $target_file;
                $sql = "UPDATE users SET profile_picture = '$file_path' WHERE id = $user_id";
                if ($conn->query($sql) === TRUE) {
                    $user['profile_picture'] = $file_path; // Update profile picture path
                    echo "The file ". htmlspecialchars(basename($_FILES["profile_picture"]["name"])). " has been uploaded.";
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
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
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 400px;
            text-align: center;
        }
        .dashboard-container h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .profile-picture {
            width: 300px;
            height: 230px;
            border-radius: 5%;
            margin-bottom: 20px;
        }
        .no-profile-picture {
            color: #888;
            margin-bottom: 20px;
        }
        .upload-btn,
        .logout-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            display: block;
            width: 100%;
        }
        .upload-btn:hover,
        .logout-btn:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            background-color: #dc3545;
        }
        .logout-btn:hover {
            background-color: #bd2130;
        }
        .file-input {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 8px;
            width: 100%;
            margin-bottom: 10px;
            box-sizing: border-box;
            cursor: pointer;
        }
    </style>
    <script>
        function updateProfilePicture(src) {
            var img = document.getElementById('profile-picture');
            img.src = src;
        }

        var inactivityTime = function () {
            var time;
            window.onload = resetTimer;
            window.onmousemove = resetTimer;
            window.onmousedown = resetTimer;
            window.ontouchstart = resetTimer;
            window.onclick = resetTimer;
            window.onkeypress = resetTimer;

            function logout() {
                alert("You have been logged out due to inactivity.");
                window.location.href = 'index.html';
            }

            function resetTimer() {
                clearTimeout(time);
                time = setTimeout(logout, 15000); // 15 seconds
            }
        };

        window.onload = function() {
            inactivityTime();
        };
    </script>
</head>
<body>
<div class="wrapper">
    <div class="dashboard-container">
        <h1>Welcome, <?php echo $user['username']; ?></h1>
        <h2>User ID: <?php echo $user['id']; ?></h2>
        <h3>Email: <?php echo $user['email']; ?></h3>
        <h3>Phone: <?php echo $user['phone']; ?></h3>
        <?php if (!empty($user['profile_picture'])): ?>
            <img src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture" class="profile-picture" id="profile-picture">
            <form action="" method="post">
                <input type="submit" value="Remove Picture" name="remove_picture">
            </form>
        <?php else: ?>
            <div class="no-profile-picture">No profile picture uploaded yet.</div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="profile_picture" class="file-input" required>
            <input type="submit" value="Upload" class="upload-btn">
        </form>
        <form action="index.html" method="post">
            <input type="submit" value="Logout" class="logout-btn" name="logout">
        </form>
    </div>
</div>
</body>
</html>
