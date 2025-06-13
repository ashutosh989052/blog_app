<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$blog_id = isset($_POST['blog_id']) ? intval($_POST['blog_id']) : 0;

$response = ['status' => 'error'];

$check = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND blog_id = ?");
$check->bind_param("ii", $user_id, $blog_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $like = $conn->prepare("INSERT INTO likes (user_id, blog_id, created_at) VALUES (?, ?, NOW())");
    $like->bind_param("ii", $user_id, $blog_id);
    $like->execute();
    $response['liked'] = true;
} else {
    $unlike = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND blog_id = ?");
    $unlike->bind_param("ii", $user_id, $blog_id);
    $unlike->execute();
    $response['liked'] = false;
}

// Get updated count
$count = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE blog_id = $blog_id")->fetch_assoc();
$response['like_count'] = $count['total'];
$response['status'] = 'success';

echo json_encode($response);
?>
