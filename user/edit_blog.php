<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch current blog
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    die("Blog not found or unauthorized.");
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $category_id = intval($_POST['category']);
    $content = $_POST['content'];
    $image_path = $blog['image'];

    // Handle image update
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $new_image_path = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $new_image_path)) {
            $image_path = $new_image_path;
        }
    }

    $stmt = $conn->prepare("UPDATE blogs SET title=?, category_id=?, content=?, image=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sissii", $title, $category_id, $content, $image_path, $blog_id, $user_id);

    if ($stmt->execute()) {
        header("Location: my_blogs.php");
        exit;
    } else {
        echo "Update failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Blog</title>
    <link rel="stylesheet" href="../assets/css/form.css">
</head>
<body>
<div class="form-container">
    <h2>Edit Blog</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>

        <label>Category:</label>
        <select name="category" required>
            <?php while ($row = $categories->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= $row['id'] == $blog['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Content:</label>
        <textarea name="content" rows="6"><?php echo htmlspecialchars($blog['content']); ?></textarea>

        <label>Update Image (optional):</label>
        <input type="file" name="image">

        <button type="submit">Update Blog</button>
    </form>
</div>
</body>
</html>
