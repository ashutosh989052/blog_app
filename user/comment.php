<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$blog_id = isset($_POST['blog_id']) ? intval($_POST['blog_id']) : 0;
$comment_text = trim($_POST['comment_text'] ?? '');

if ($comment_text === '') {
    echo json_encode(['status' => 'error', 'message' => 'Comment cannot be empty.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO comments (user_id, blog_id, comment, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $user_id, $blog_id, $comment_text);
$stmt->execute();

// Fetch newly added comment with username
$last_id = $conn->insert_id;
$fetch = $conn->prepare("SELECT comments.comment, users.username, comments.created_at 
                         FROM comments 
                         JOIN users ON comments.user_id = users.id 
                         WHERE comments.id = ?");
$fetch->bind_param("i", $last_id);
$fetch->execute();
$result = $fetch->get_result()->fetch_assoc();

echo json_encode([
    'status' => 'success',
    'comment' => htmlspecialchars($result['comment']),
    'username' => htmlspecialchars($result['username']),
    'created_at' => date('d M Y, h:i A', strtotime($result['created_at']))
]);
