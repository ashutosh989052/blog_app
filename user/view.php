<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'] ?? null;

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

// Like Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    $user_id = $_SESSION['user_id'] ?? null;
    if ($user_id && $blog_id) {
        $check = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND blog_id = ?");
        $check->bind_param("ii", $user_id, $blog_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $like = $conn->prepare("INSERT INTO likes (user_id, blog_id, created_at) VALUES (?, ?, NOW())");
            $like->bind_param("ii", $user_id, $blog_id);
            $like->execute();
        }
    }
    header("Location: view.php?id=$blog_id");
    exit;
}

// Comment Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && !empty(trim($_POST['comment_text']))) {
    $user_id = $_SESSION['user_id'] ?? null;
    $comment_text = trim($_POST['comment_text']);

    if ($user_id && $comment_text) {
        $insert = $conn->prepare("INSERT INTO comments (user_id, blog_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $insert->bind_param("iis", $user_id, $blog_id, $comment_text);
        $insert->execute();
    }
    header("Location: view.php?id=$blog_id");
    exit;
}

// Check if user has already liked this blog
$alreadyLiked = false;
if ($user_id) {
    $like_check = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND blog_id = ?");
    $like_check->bind_param("ii", $user_id, $blog_id);
    $like_check->execute();
    $like_check->store_result();
    $alreadyLiked = $like_check->num_rows > 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($blog['title']) ?> | Blogify</title>
    <link rel="stylesheet" href="../assets/css/view.css">
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include '../includes/user_navbar.php'; ?>

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

<!-- Interaction Bar -->
<div class="interaction-bar">
    <form method="POST" style="display: inline;">
<button class="like-btn <?= $alreadyLiked ? 'liked' : '' ?>" 
        type="button" 
        id="likeBtn" 
        data-liked="<?= $alreadyLiked ? '1' : '0' ?>" 
        data-blog="<?= $blog_id ?>">
    <i class="fas fa-heart"></i> <span id="likeText"><?= $alreadyLiked ? 'Liked' : 'Like' ?></span> (<span id="likeCount"><?= $likeCount ?></span>)
</button>


    </form>
<a href="#comment-box" class="comment-btn <?= $alreadyCommented ? 'commented' : '' ?>">
    <i class="fas fa-comment"></i> <?= $alreadyCommented ? 'Commented' : 'Comment' ?> (<?= $commentCount ?>)
</a>

</div>
<!-- Comments Section -->
<div class="comment-section">
    <h3><i class="fas fa-comments"></i> Comments</h3>

    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Comment Form -->
    <form id="commentForm" class="comment-form">
        <textarea name="comment_text" id="comment-box" rows="3" placeholder="Write your comment..." required></textarea>
        <div class="form-footer">
            <span class="char-count" id="charCount">0/300</span>
            <button type="submit" class="comment-submit-btn">Post Comment</button>
        </div>
    </form>
    <?php else: ?>
        <p><a href="../login.php">Log in</a> to post a comment.</p>
    <?php endif; ?>

    <!-- Comment List -->
    <div id="comment-list">
    <?php
    $comments_query = $conn->query("SELECT comments.comment, users.username, comments.created_at 
                                    FROM comments 
                                    JOIN users ON comments.user_id = users.id 
                                    WHERE blog_id = $blog_id 
                                    ORDER BY comments.created_at DESC");

    if ($comments_query && $comments_query->num_rows > 0):
        while ($cmt = $comments_query->fetch_assoc()):
    ?>
        <div class="comment">
            <strong><?= htmlspecialchars($cmt['username']) ?></strong>
            <p><?= nl2br(htmlspecialchars($cmt['comment'])) ?></p>
            <span class="time"><?= date('d M Y, h:i A', strtotime($cmt['created_at'])) ?></span>
        </div>
    <?php endwhile; else: ?>
        <p class="no-comments">No comments yet. Be the first to comment!</p>
    <?php endif; ?>
    </div>
</div>

<!-- Character Count Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const textarea = document.querySelector("textarea[name='comment_text']");
    const charCount = document.getElementById("charCount");

    if (textarea) {
        textarea.addEventListener("input", () => {
            const length = textarea.value.length;
            charCount.textContent = `${length}/300`;
            if (length > 300) {
                textarea.value = textarea.value.substring(0, 300);
                charCount.textContent = "300/300";
            }
        });
    }
});
</script>

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
<script>
document.addEventListener('DOMContentLoaded', () => {
    const likeBtn = document.getElementById('likeBtn');

    likeBtn.addEventListener('click', () => {
        const blogId = likeBtn.dataset.blog;
        
        fetch('like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `blog_id=${blogId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('likeCount').textContent = data.like_count;
                const likeText = document.getElementById('likeText');

                if (data.liked) {
                    likeBtn.classList.add('liked');
                    likeText.textContent = 'Liked';
                } else {
                    likeBtn.classList.remove('liked');
                    likeText.textContent = 'Like';
                }
            } else if (data.status === 'unauthorized') {
                alert("Please log in to like this post.");
                window.location.href = '../login.php';
            }
        })
        .catch(err => {
            console.error('Error:', err);
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const commentForm = document.getElementById('commentForm');
    const commentList = document.getElementById('comment-list');
    const commentBox = document.getElementById('comment-box');
    const charCount = document.getElementById('charCount');
    const blogId = <?= $blog_id ?>;

    // Submit comment
    commentForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const commentText = commentBox.value.trim();
        if (!commentText) return;

        fetch('comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `blog_id=${blogId}&comment_text=${encodeURIComponent(commentText)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Create comment HTML
                const newComment = document.createElement('div');
                newComment.className = 'comment';
                newComment.innerHTML = `
                    <strong>${data.username}</strong>
                    <p>${data.comment.replace(/\n/g, "<br>")}</p>
                    <span class="time">${data.created_at}</span>
                `;
                // Prepend comment
                commentList.prepend(newComment);
                commentBox.value = '';
                charCount.textContent = '0/300';
            } else if (data.status === 'unauthorized') {
                alert("Please log in to post a comment.");
                window.location.href = '../login.php';
            } else {
                alert(data.message || "An error occurred.");
            }
        })
        .catch(err => {
            console.error('Error:', err);
        });
    });

    // Character count live update
    commentBox.addEventListener("input", () => {
        let length = commentBox.value.length;
        if (length > 300) {
            commentBox.value = commentBox.value.substring(0, 300);
            length = 300;
        }
        charCount.textContent = `${length}/300`;
    });
});
</script>


<?php include '../footer.php'; ?>
</body>
</html>
