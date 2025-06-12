<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch user blogs
$sql = "SELECT blogs.*, categories.name AS category_name 
        FROM blogs 
        JOIN categories ON blogs.category_id = categories.id 
        WHERE blogs.user_id = ? 
        ORDER BY blogs.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Blogs</title>
  <link rel="stylesheet" href="../assets/css/my_blogs.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

  <nav class="navbar">
    <div class="logo">Blogify</div>
    <ul class="nav-links">
      <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
      <li><a href="post_blog.php"><i class="fas fa-pen"></i> Post Blog</a></li>
      <li><a href="my_blogs.php" class="active"><i class="fas fa-user-edit"></i> My Blogs</a></li>
      <li><a href="../index.php"><i class="fas fa-globe"></i> Explore</a></li>
      <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <div class="container">
    <h2>My Blogs</h2>

    <?php if ($result->num_rows > 0): ?>
      <div class="blog-list">
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="blog-card">
            <?php if (!empty($row['image'])): ?>
              <img src="<?php echo $row['image']; ?>" alt="Blog Image">
            <?php endif; ?>
            <div class="blog-details">
              <h3><?php echo htmlspecialchars($row['title']); ?></h3>
              <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category_name']); ?></p>
              <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
              <div class="actions">
                <a href="view_blog.php?id=<?php echo $row['id']; ?>" class="btn view"><i class="fas fa-eye"></i> View</a>
                <a href="edit_blog.php?id=<?php echo $row['id']; ?>" class="btn edit"><i class="fas fa-edit"></i> Edit</a>
                <a href="delete_blog.php?id=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this blog?')"><i class="fas fa-trash"></i> Delete</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="no-blogs">You haven’t posted any blogs yet.</p>
    <?php endif; ?>
  </div>

</body>
</html>
