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
  <link rel="stylesheet" href="../assets/css/user_navbar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<?php include '../includes/user_navbar.php'; ?>

<main class="my-blogs-container">
  <h1><i class="fas fa-user-pen"></i> <?= htmlspecialchars($username) ?>'s Blogs</h1>

  <?php if ($result->num_rows > 0): ?>
    <div class="blog-grid">
      <?php while ($blog = $result->fetch_assoc()): ?>
        <div class="blog-card">
          <?php if ($blog['image']): ?>
            <div class="blog-img">
              <img src="<?= htmlspecialchars($blog['image']) ?>" alt="Blog image">
            </div>
          <?php endif; ?>
          <div class="blog-info">
            <h2><?= htmlspecialchars($blog['title']) ?></h2>
            <p><i class="fas fa-layer-group"></i> <?= htmlspecialchars($blog['category_name']) ?></p>
            <p><i class="fas fa-calendar-alt"></i> <?= date("d M Y", strtotime($blog['created_at'])) ?></p>
<div class="blog-actions">
  <a href="view.php?id=<?= $blog['id'] ?>" class="view-btn"><i class="fas fa-eye"></i> View</a>
  <a href="edit_blog.php?id=<?= $blog['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
  <form action="delete_blog.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this blog?');" style="display:inline;">
    <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
    <button type="submit" class="delete-btn"><i class="fas fa-trash"></i> Delete</button>
  </form>
</div>

          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <?php if (isset($_GET['deleted'])): ?>
  <p class="success-msg"><i class="fas fa-check-circle"></i> Blog deleted successfully.</p>
<?php endif; ?>

  <?php else: ?>
    <p class="no-blogs">You haven’t written any blogs yet. <a href="../blogs/create_blog.php">Start writing</a>!</p>
  <?php endif; ?>
</main>

</body>
</html>
