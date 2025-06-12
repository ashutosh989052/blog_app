<?php
require_once '../includes/db.php';

// Get all categories
$catRes = $conn->query("SELECT * FROM categories");

// Apply filter/search
$category_id = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT blogs.id, blogs.title, blogs.image, categories.name AS category 
        FROM blogs 
        JOIN categories ON blogs.category_id = categories.id 
        WHERE 1";

if (!empty($category_id)) {
    $sql .= " AND blogs.category_id = " . intval($category_id);
}

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (blogs.title LIKE '%$search%' OR blogs.content LIKE '%$search%')";
}

$sql .= " ORDER BY blogs.created_at DESC";
$blogs = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Home</title>
    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">MyBlog</div>
    <div class="nav-links">
        <a href="../user/login.php">Login</a>
        <a href="../user/register.php">Register</a>
    </div>
</nav>

<div class="filter-bar">
    <form method="GET" style="display: flex; gap: 10px;">
        <select name="category">
            <option value="">-- All Categories --</option>
            <?php while ($cat = $catRes->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $category_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="search" placeholder="Search blogs..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Filter</button>
    </form>
</div>

<div class="content">
    <h2>📚 Blogs</h2>
    <div class="blog-grid">
        <?php if ($blogs->num_rows == 0): ?>
            <p>No blogs found.</p>
        <?php else: ?>
            <?php while ($row = $blogs->fetch_assoc()):
                $blog_id = $row['id'];
                $likeCount = $conn->query("SELECT COUNT(*) as total FROM likes WHERE blog_id = $blog_id")->fetch_assoc()['total'];
                $commentCount = $conn->query("SELECT COUNT(*) as total FROM comments WHERE blog_id = $blog_id")->fetch_assoc()['total'];
            ?>
                <div class="blog-card">
                    <?php if ($row['image']): ?>
                        <img src="<?= $row['image'] ?>" alt="blog" class="thumb">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><b>Category:</b> <?= htmlspecialchars($row['category']) ?></p>
                    <p>👍 <?= $likeCount ?> Likes &nbsp; 💬 <?= $commentCount ?> Comments</p>
                    <a href="view.php?id=<?= $row['id'] ?>" class="btn">Read</a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
