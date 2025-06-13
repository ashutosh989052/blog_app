<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get totals
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalBlogs = $conn->query("SELECT COUNT(*) AS total FROM blogs")->fetch_assoc()['total'];
$totalComments = $conn->query("SELECT COUNT(*) AS total FROM comments")->fetch_assoc()['total'];
$totalLikes = $conn->query("SELECT COUNT(*) AS total FROM likes")->fetch_assoc()['total'];
?>

<?php include '../includes/admin_navbar.php'; ?>

<link rel="stylesheet" href="../assets/css/admin_dashboard.css">

<div class="dashboard-container">
    <h1 class="dashboard-title">Welcome, Admin</h1>
    <div class="stats-grid">
        <div class="stat-card users">
            <h3>Total Users</h3>
            <p><?= $totalUsers ?></p>
        </div>
        <div class="stat-card blogs">
            <h3>Total Blogs</h3>
            <p><?= $totalBlogs ?></p>
        </div>
        <div class="stat-card comments">
            <h3>Total Comments</h3>
            <p><?= $totalComments ?></p>
        </div>
        <div class="stat-card likes">
            <h3>Total Likes</h3>
            <p><?= $totalLikes ?></p>
        </div>
    </div>
</div>
