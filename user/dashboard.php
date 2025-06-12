<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

  <nav class="navbar">
    <div class="logo">Blogify</div>
    <ul class="nav-links">
      <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
      <li><a href="post_blog.php"><i class="fas fa-pen"></i> Post Blog</a></li>
      <li><a href="my_blogs.php"><i class="fas fa-user-edit"></i> My Blogs</a></li>
      <li><a href="../index.php"><i class="fas fa-globe"></i> Explore</a></li>
      <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <section class="dashboard-content">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>You can manage your blogs, explore others' blogs, and post new content using the navigation bar above.</p>
  </section>

</body>
</html>
