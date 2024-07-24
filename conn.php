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
else
echo"Connection Success";
?>