<?php
session_start();
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
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

// Handle like/unlike
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    if ($user_id) {
        $check = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND blog_id = ?");
        $check->bind_param("ii", $user_id, $blog_id);
        $check->execute();
        $liked = $check->get_result()->num_rows;

        if ($liked) {
            $del = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND blog_id = ?");
            $del->bind_param("ii", $user_id, $blog_id);
            $del->execute();
        } else {
            $ins = $conn->prepare("INSERT INTO likes (user_id, blog_id) VALUES (?, ?)");
            $ins->bind_param("ii", $user_id, $blog_id);
            $ins->execute();
        }
    }
    header("Location: view_blog.php?id=$blog_id");
    exit;
}

// Handle comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $comment = trim($_POST['comment']);
    if ($user_id && !empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, blog_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $blog_id, $comment);
        $stmt->execute();
    }
    header("Location: view_blog.php?id=$blog_id");
    exit;
}

$likeCount = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE blog_id = $blog_id")->fetch_assoc()['total'];

$commentStmt = $conn->prepare("SELECT comments.*, users.username FROM comments 
                               JOIN users ON comments.user_id = users.id 
                               WHERE blog_id = ? ORDER BY created_at DESC");
$commentStmt->bind_param("i", $blog_id);
$commentStmt->execute();
$commentResult = $commentStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($blog['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/view_blog.css">
</head>
<body>
<div class="blog-view">
    <h1><?= htmlspecialchars($blog['title']) ?></h1>
    <p><b>Author:</b> <?= htmlspecialchars($blog['username']) ?> | 
       <b>Category:</b> <?= htmlspecialchars($blog['category_name']) ?> | 
       <b>Date:</b> <?= date('d M Y', strtotime($blog['created_at'])) ?></p>

    <?php if ($blog['image']): ?>
        <img src="<?= $blog['image'] ?>" class="blog-image">
    <?php endif; ?>

    <div class="blog-content"><?= nl2br(htmlspecialchars($blog['content'])) ?></div>

    <form method="POST"><button name="like" class="like-btn">👍 Like (<?= $likeCount ?>)</button></form>

    <h3>💬 Comments</h3>
    <form method="POST">
        <textarea name="comment" rows="3" required placeholder="Add a comment..."></textarea>
        <br><button name="comment_submit">Post Comment</button>
    </form>

    <div class="comments">
    <?php while ($row = $commentResult->fetch_assoc()): ?>
        <div class="comment">
            <strong><?= htmlspecialchars($row['username']) ?>:</strong>
            <p><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
            <small><?= date('d M Y H:i', strtotime($row['created_at'])) ?></small>
            <hr>
        </div>
    <?php endwhile; ?>
    </div>
</div>
</body>
</html>
