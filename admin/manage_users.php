<?php
session_start();
require_once '../includes/db.php';

$users = $conn->query("SELECT * FROM users");

include 'navbar_admin.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Users</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="container">
  <h2>Users</h2>
  <table>
    <tr>
      <th>ID</th><th>Username</th><th>Email</th><th>Actions</th>
    </tr>
    <?php while($user = $users->fetch_assoc()): ?>
      <tr>
        <td><?= $user['id'] ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td>
          <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
