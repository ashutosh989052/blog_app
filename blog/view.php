<?php
require_once '../includes/db.php';

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT blogs.*, users.username, categories.name AS category_name 
                        FROM blogs 
                        JOIN users ON blogs.user_id = users.id 
                        JOIN categories ON blogs.category_id = categories.id 
                        WHERE blogs.id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) die("Blog not found.");

$likeCount = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE blog_id = $blog_id")->fetch_assoc()['total'];
$commentCount = $conn->query("SELECT COUNT(*) AS total FROM comments WHERE blog_id = $blog_id")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($blog['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/view_blog.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <div class="logo"><a href="../index.php"><img src="../assets/images/logo.png" alt="Logo"></a></div>
        <ul class="nav-menu">
            <li><a href="../index.php">Home</a></li>
            <li><a href="../user/dashboard.php">Dashboard</a></li>
            <li><a href="../user/post_blog.php">Post</a></li>
            <li><a href="../user/my_blogs.php">My Blogs</a></li>
            <li><a href="../user/logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<main class="blog-container">
    <div class="blog-header">
        <h1><?= htmlspecialchars($blog['title']) ?></h1>
        <div class="meta-info">
            <span>By <?= htmlspecialchars($blog['username']) ?></span> |
            <span>Category: <?= htmlspecialchars($blog['category_name']) ?></span> |
            <span><?= date('d M Y', strtotime($blog['created_at'])) ?></span>
        </div>
    </div>

    <?php if ($blog['image']): ?>
        <div class="blog-image">
            <img src="<?= $blog['image'] ?>" alt="Blog Cover Image">
        </div>
    <?php endif; ?>

    <article class="blog-content">
        <?= $blog['content'] ?>
    </article>

    <div class="blog-stats">
        <span><?= $likeCount ?> Likes</span>
        <span><?= $commentCount ?> Comments</span>
    </div>

    <div class="login-note">
        <p>Please <a href="../user/login.php">login</a> to like or comment on this blog.</p>
    </div>
</main>

</body>
</html>
