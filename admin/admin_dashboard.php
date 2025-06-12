<?php
session_start();
require_once '../includes/db.php';

// Optional: check if admin is logged in

include 'navbar_admin.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="container">
  <h2>Welcome Admin 👨‍💼</h2>
  <p>Use the navbar to manage users and blogs.</p>
</div>
</body>
</html>
