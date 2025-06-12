<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$search = $_GET['search'] ?? '';

// Fetch user's blogs
$userBlogsStmt = $conn->prepare("SELECT blogs.*, categories.name AS category_name 
                                FROM blogs 
                                JOIN categories ON blogs.category_id = categories.id 
                                WHERE blogs.user_id = ? AND blogs.title LIKE ?");
$likeSearch = "%$search%";
$userBlogsStmt->bind_param("is", $user_id, $likeSearch);
$userBlogsStmt->execute();
$userBlogs = $userBlogsStmt->get_result();

// Fetch others' blogs
$otherBlogsStmt = $conn->prepare("SELECT blogs.*, users.username, categories.name AS category_name 
                                 FROM blogs 
                                 JOIN users ON blogs.user_id = users.id 
                                 JOIN categories ON blogs.category_id = categories.id 
                                 WHERE blogs.user_id != ? AND blogs.title LIKE ?");
$otherBlogsStmt->bind_param("is", $user_id, $likeSearch);
$otherBlogsStmt->execute();
$otherBlogs = $otherBlogsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($username) ?>'s Dashboard</title>
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php include '../includes/user_navbar.php'; ?>

<main class="dashboard-wrapper">
    <div class="dashboard-header">
        <h1>Hello, <?= htmlspecialchars($username) ?> 👋</h1>
        <form class="search-bar" method="get">
            <input type="text" name="search" placeholder="Search blog titles..." value="<?= htmlspecialchars($search) ?>">
            <button><i class="fas fa-search"></i></button>
        </form>
    </div>

    <section class="blogs-section">
        <h2><i class="fas fa-user-pen"></i> Your Blogs</h2>
        <div class="blog-grid">
            <?php if ($userBlogs->num_rows > 0): ?>
                <?php while ($blog = $userBlogs->fetch_assoc()): ?>
                    <div class="blog-card fade-in">
                        <h3><?= htmlspecialchars($blog['title']) ?></h3>
                        <p><i class="fas fa-folder"></i> <?= htmlspecialchars($blog['category_name']) ?></p>
                        <p><i class="fas fa-calendar-day"></i> <?= date('d M Y', strtotime($blog['created_at'])) ?></p>
                        <a href="view.php?id=<?= $blog['id'] ?>" class="read-btn">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-msg">You haven't written any blogs yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="blogs-section">
        <h2><i class="fas fa-users"></i> Other Users' Blogs</h2>
        <div class="blog-grid">
            <?php if ($otherBlogs->num_rows > 0): ?>
                <?php while ($blog = $otherBlogs->fetch_assoc()): ?>
                    <div class="blog-card fade-in">
                        <h3><?= htmlspecialchars($blog['title']) ?></h3>
                        <p><i class="fas fa-user"></i> <?= htmlspecialchars($blog['username']) ?></p>
                        <p><i class="fas fa-folder"></i> <?= htmlspecialchars($blog['category_name']) ?> | 
                           <i class="fas fa-calendar-day"></i> <?= date('d M Y', strtotime($blog['created_at'])) ?></p>
                        <a href="view.php?id=<?= $blog['id'] ?>" class="read-btn">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-msg">No blogs from other users match your search.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>
