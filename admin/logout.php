<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged Out | Blogify Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="3;url=login.php">
    <link rel="stylesheet" href="../assets/css/logout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="logout-container">
        <div class="message-box">
            <i class="fas fa-check-circle"></i>
            <h2>You have been logged out</h2>
            <p>Redirecting to login page in a few seconds...</p>
            <a href="login.php" class="login-link">Go to Login</a>
        </div>
    </div>
</body>
</html>
