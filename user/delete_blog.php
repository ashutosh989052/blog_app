<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $blog_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Ensure user owns the blog
    $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();

    header("Location: my_blogs.php");
    exit;
}
?>
