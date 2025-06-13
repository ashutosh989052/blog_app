<?php
require_once '../includes/db.php';

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT blogs.*, users.username, categories.name AS category_name, blogs.category_id 
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

// Fetch related blogs (securely)
$related_stmt = $conn->prepare("SELECT id, title, image FROM blogs WHERE category_id = ? AND id != ? ORDER BY created_at DESC LIMIT 3");
$related_stmt->bind_param("ii", $blog['category_id'], $blog_id);
$related_stmt->execute();
$related = $related_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($blog['title']) ?> | Blogify</title>
    <link rel="stylesheet" href="../assets/css/view.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include '../navbar.php'; ?>

<main class="main-container">
    <div class="blog-reader">
        <h1><?= htmlspecialchars($blog['title']) ?></h1>
        <div class="meta-info">
            <span><i class="fas fa-user"></i> <?= htmlspecialchars($blog['username']) ?></span>
            <span><i class="fas fa-folder"></i> <?= htmlspecialchars($blog['category_name']) ?></span>
            <span><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($blog['created_at'])) ?></span>
        </div>

        <?php if ($blog['image']): ?>
            <div class="blog-image">
                <img src="<?= $blog['image'] ?>" alt="Blog Cover Image">
            </div>
        <?php endif; ?>

        <article class="blog-content">
            <?= $blog['content'] ?>
        </article>

        <div class="interaction-bar">
            <button class="like-btn"><i class="fas fa-heart"></i> Like (<?= $likeCount ?>)</button>
            <button class="comment-btn"><i class="fas fa-comment"></i> Comment (<?= $commentCount ?>)</button>
        </div>

        <!-- Comments Section -->
        <div class="comment-section">
    <h3><i class="fas fa-comments"></i> Comments</h3>

    <?php
    $comments_query = $conn->query("SELECT comments.comment, users.username, comments.created_at 
                                    FROM comments 
                                    JOIN users ON comments.user_id = users.id 
                                    WHERE blog_id = $blog_id 
                                    ORDER BY comments.created_at DESC");

    if ($comments_query && $comments_query->num_rows > 0) {
        while ($cmt = $comments_query->fetch_assoc()) {
            ?>
            <div class="comment">
                <strong><?= htmlspecialchars($cmt['username']) ?></strong>
                <p><?= nl2br(htmlspecialchars($cmt['comment'])) ?></p>
                <span class="time"><?= date('d M Y, h:i A', strtotime($cmt['created_at'])) ?></span>
            </div>
            <?php
        }
    } else {
        echo '<p class="no-comments">No comments yet. Be the first to comment!</p>';
    }
    ?>
</div>

    <!-- Related Blogs Section -->
    <section class="related-blogs">
        <h2><i class="fas fa-compass"></i> You Might Also Like</h2>
        <div class="blog-grid">
            <?php if ($related->num_rows > 0): ?>
                <?php while ($rel = $related->fetch_assoc()): ?>
                    <div class="blog-card">
                        <div class="blog-thumbnail">
                            <?php if ($rel['image']): ?>
                                <img src="<?= $rel['image'] ?>" alt="Related Blog Thumbnail">
                            <?php else: ?>
                                <img src="../assets/images/default-blog.jpg" alt="No image available">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h3><?= htmlspecialchars($rel['title']) ?></h3>
                            <a href="view.php?id=<?= $rel['id'] ?>" class="read-btn"><i class="fas fa-book-reader"></i> Read More</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-comments">No related blogs found.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- Login Required Modal -->
<div class="login-popup" id="loginPopup">
    <div class="login-popup-content">
        <span class="close-popup" onclick="closeLoginPopup()">&times;</span>
        <h3><i class="fas fa-sign-in-alt"></i> Please log in to continue</h3>
        <p>You need to log in or register to like or comment on this blog.</p>
        <div class="popup-buttons">
            <a href="../user/login.php" class="popup-btn">Login</a>
            <a href="../user/register.php" class="popup-btn secondary">Register</a>
        </div>
    </div>
</div>

<script>
    function openLoginPopup() {
        document.getElementById("loginPopup").style.display = "flex";
    }

    function closeLoginPopup() {
        document.getElementById("loginPopup").style.display = "none";
    }

    // Attach listeners for non-auth users
    <?php if (!isset($_SESSION['user_id'])): ?>
        document.querySelector('.like-btn').addEventListener('click', openLoginPopup);
        document.querySelector('.comment-btn').addEventListener('click', openLoginPopup);
    <?php endif; ?>
</script>

<?php include '../footer.php'; ?>
</body>
</html>
