<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) exit("Unauthorized");

// Delete comment
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM comments WHERE id = $id");
}

// Search comments
$search = $_GET['search'] ?? '';
$comments = $conn->query("SELECT comments.id, comments.comment, users.username, blogs.title 
                          FROM comments 
                          JOIN users ON comments.user_id = users.id 
                          JOIN blogs ON comments.blog_id = blogs.id 
                          WHERE (comments.comment LIKE '%$search%' OR users.username LIKE '%$search%' OR blogs.title LIKE '%$search%') 
                          ORDER BY comments.created_at DESC");
?>
<?php include '../includes/admin_navbar.php'; ?>
<link rel="stylesheet" href="../assets/css/admin_comments.css">

<div class="page-container">
    <h2 class="page-title">Manage Comments</h2>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by comment, user, or blog..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="fas fa-search"></i> Search</button>
    </form>

    <div class="table-wrapper">
        <table class="comment-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Blog</th>
                    <th>Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = $comments->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($c['username']) ?></td>
                    <td><?= htmlspecialchars($c['title']) ?></td>
                    <td><?= htmlspecialchars($c['comment']) ?></td>
                    <td>
                        <a href="?delete=<?= $c['id'] ?>" class="btn delete" onclick="return confirm('Delete this comment?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
