<?php
require_once 'includes/db.php';

$catRes = $conn->query("SELECT * FROM categories");
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Blogs</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>


<div class="filter-bar">
    <form method="GET" class="filter-form">
        <select name="category">
            <option value="">All Categories</option>
            <?php while ($cat = $catRes->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $category_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="search" placeholder="Search blogs..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="container">
    <h2><i class="fas fa-pen-nib"></i> Trending Blogs</h2>
    <div class="blog-grid">
        <?php if ($blogs->num_rows == 0): ?>
            <p class="empty">No blogs found.</p>
        <?php else: ?>
            <?php while ($row = $blogs->fetch_assoc()): 
                $blog_id = $row['id'];
                $likeCount = $conn->query("SELECT COUNT(*) as total FROM likes WHERE blog_id = $blog_id")->fetch_assoc()['total'];
                $commentCount = $conn->query("SELECT COUNT(*) as total FROM comments WHERE blog_id = $blog_id")->fetch_assoc()['total'];
            ?>
            <div class="blog-card">
                <?php if ($row['image']): ?>
                    <img src="<?= htmlspecialchars(str_replace('../', '', $row['image'])) ?>" alt="blog" class="thumb">
                <?php endif; ?>
                <div class="card-body">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p class="category"><i class="fas fa-tag"></i> <?= htmlspecialchars($row['category']) ?></p>
                    <div class="stats">
                        <span><i class="fas fa-thumbs-up"></i> <?= $likeCount ?></span>
                        <span><i class="fas fa-comments"></i> <?= $commentCount ?></span>
                    </div>
                    <a href="blog/view.php?id=<?= $blog_id ?>" class="read-btn"><i class="fas fa-book-reader"></i> Read Blog</a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
