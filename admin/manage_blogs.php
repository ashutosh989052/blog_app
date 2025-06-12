<?php
session_start();
require_once '../includes/db.php';

$blogs = $conn->query("SELECT blogs.*, users.username FROM blogs JOIN users ON blogs.user_id = users.id");

include 'navbar_admin.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Blogs</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="container">
  <h2>All Blogs</h2>
  <table>
    <tr>
      <th>ID</th><th>Title</th><th>Author</th><th>Date</th><th>Actions</th>
    </tr>
    <?php while($blog = $blogs->fetch_assoc()): ?>
      <tr>
        <td><?= $blog['id'] ?></td>
        <td><?= htmlspecialchars($blog['title']) ?></td>
        <td><?= htmlspecialchars($blog['username']) ?></td>
        <td><?= date('d M Y', strtotime($blog['created_at'])) ?></td>
        <td>
          <a href="delete_blog.php?id=<?= $blog['id'] ?>" onclick="return confirm('Delete this blog?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
