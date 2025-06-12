<?php
// Database credentials
$host = "localhost";
$user = "root";
$password = "";
$database = "blog_app_db";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8");

// For testing (you can comment this out later)
# echo "Database connected successfully";
?>
