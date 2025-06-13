<?php $current = basename($_SERVER['PHP_SELF']); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin_navbar.css">
<nav class="minimal-navbar">
    <div class="nav-container">
        <div class="nav-left">
            <div class="brand">Blogify Admin</div>
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <ul class="nav-menu" id="navMenu">
            <li><a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="manage_users.php" class="<?= $current === 'manage_users.php' ? 'active' : '' ?>">Users</a></li>
            <li><a href="manage_categories.php" class="<?= $current === 'manage_categories.php' ? 'active' : '' ?>">Categories</a></li>
            <li><a href="manage_blogs.php" class="<?= $current === 'manage_blogs.php' ? 'active' : '' ?>">Blogs</a></li>
            <li><a href="manage_comments.php" class="<?= $current === 'manage_comments.php' ? 'active' : '' ?>">Comments</a></li>
            <li><a href="messages.php" class="<?= $current === 'messages.php' ? 'active' : '' ?>">Messages</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </div>
</nav>
    <script>
document.getElementById('hamburger').addEventListener('click', function () {
    const nav = document.getElementById('navLinks');
    nav.classList.toggle('show');
});
</script>

