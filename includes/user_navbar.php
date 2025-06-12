<?php
//session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}
$username = $_SESSION['username'] ?? 'User';
?>

<nav class="user-navbar">
    <div class="nav-container">
        <a href="../index.php" class="nav-brand">
            <i class="fas fa-pen-nib"></i> Blogify
        </a>

        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="menu-icon">
            <i class="fas fa-bars"></i>
        </label>

        <ul class="nav-links">
            <li><a href="../user/dashboard.php"><i class="fas fa-user-cog"></i> Dashboard</a></li>
            <li><a href="../user/my_blogs.php"><i class="fas fa-book"></i> View My Blogs</a></li>
            <li><a href="../user/post_blog.php"><i class="fas fa-plus"></i> Create Blog</a></li>
            <li class="nav-username"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></li>
            <li><a href="../user/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</nav>
