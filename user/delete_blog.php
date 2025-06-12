<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$blog_id = intval($_POST['blog_id']);
$user_id = $_SESSION['user_id'];

// Ensure the blog belongs to the user
$stmt = $conn->prepare("DELETE FROM blogs WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $user_id);

if ($stmt->execute()) {
    header("Location: my_blogs.php?deleted=1");
    exit;
} else {
    echo "Failed to delete blog.";
}
?>
