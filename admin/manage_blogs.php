<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) exit("Unauthorized");

// Soft delete blog
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("UPDATE blogs SET deleted_at = NOW() WHERE id = $id");
}

// Restore blog
if (isset($_GET['restore'])) {
    $id = intval($_GET['restore']);
    $conn->query("UPDATE blogs SET deleted_at = NULL WHERE id = $id");
}

// Search blogs
$search = $_GET['search'] ?? '';
$sql = "SELECT blogs.*, users.username FROM blogs 
        JOIN users ON blogs.user_id = users.id 
        WHERE (blogs.title LIKE '%$search%' OR users.username LIKE '%$search%') 
        ORDER BY blogs.created_at DESC";
$blogs = $conn->query($sql);
?>
<?php include '../includes/admin_navbar.php'; ?>
<link rel="stylesheet" href="../assets/css/admin_blogs.css">

<div class="page-container">
    <h2 class="page-title">Manage Blogs</h2>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by blog title or user..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="fas fa-search"></i> Search</button>
    </form>

    <div class="table-wrapper">
        <table class="blog-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>User</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = $blogs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td><?= htmlspecialchars($b['username']) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($b['created_at'])) ?></td>
                    <td>
                        <?= $b['deleted_at'] ? '<span class="status-deleted">Deleted</span>' : '<span class="status-active">Active</span>' ?>
                    </td>
                    <td>
                        <?php if ($b['deleted_at']): ?>
                            <a href="?restore=<?= $b['id'] ?>" class="btn restore">Restore</a>
                        <?php else: ?>
                            <a href="?delete=<?= $b['id'] ?>" class="btn delete" onclick="return confirm('Soft delete this blog?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
