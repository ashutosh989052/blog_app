<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) exit("Unauthorized");

// Add category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_category'])) {
    $cat = trim($_POST['new_category']);
    if (!empty($cat)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $cat);
        $stmt->execute();
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id = $id");
}

$cats = $conn->query("SELECT * FROM categories ORDER BY created_at DESC");
?>
<?php include '../includes/admin_navbar.php'; ?>
<link rel="stylesheet" href="../assets/css/admin_categories.css">

<div class="page-container">
    <h2 class="page-title">Manage Categories</h2>

    <form method="POST" class="add-form">
        <input type="text" name="new_category" placeholder="Add new category..." required>
        <button type="submit"><i class="fas fa-plus"></i> Add</button>
    </form>

    <div class="table-wrapper">
        <table class="category-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cat = $cats->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($cat['created_at'])) ?></td>
                    <td>
                        <a href="?delete=<?= $cat['id'] ?>" class="btn delete"
                           onclick="return confirm('Delete this category?')">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
