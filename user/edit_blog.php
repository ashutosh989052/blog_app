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

$message = "";
$redirect = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $category_id = intval($_POST['category']);
    $content = $_POST['content'];
    $image_path = $blog['image'];

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
        $message = "Blog updated successfully!";
        $redirect = true;
    } else {
        $message = "Update failed. Try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Blog</title>
    <link rel="stylesheet" href="../assets/css/edit_blog.css">
    <link rel="stylesheet" href="../assets/css/user_navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tiny.cloud/1/lhsd6a2jh0ou98943c0fu9geycf6bpqg54ntikoced9u95jw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'image link lists code',
            toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright | bullist numlist | image link code',
            height: 300
        });
    </script>
</head>
<body>
<?php include '../includes/user_navbar.php'; ?>

<div class="form-container">
    <h2>Edit Blog</h2>
    <?php if ($message): ?>
        <p class="msg"><?= $message ?></p>
        <?php if ($redirect): ?>
            <script>
                setTimeout(() => {
                    window.location.href = "my_blogs.php";
                }, 2000);
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <form id="editBlogForm" method="POST" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" required>

        <label>Category:</label>
        <select name="category" required>
            <?php while ($row = $categories->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= $row['id'] == $blog['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Content:</label>
        <textarea id="content" name="content"><?= htmlspecialchars($blog['content']) ?></textarea>

        <label>Current Image:</label><br>
        <?php if ($blog['image']): ?>
            <img id="previewOld" src="<?= $blog['image'] ?>" width="100%">
        <?php endif; ?>

        <label>Update Image (optional):</label>
        <input type="file" name="image" id="imageInput">
        <div id="previewContainer">
            <img id="previewImage" src="#" style="display:none;">
        </div>

        <button type="button" onclick="showModal()">Update Blog</button>
    </form>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to update this blog?</p>
        <button onclick="submitForm()" type="submit">Yes, Update</button>
        <button onclick="closeModal()" type="button">Cancel</button>
    </div>
</div>

<script>
function showModal() {
    document.getElementById('confirmModal').style.display = 'block';
}
function closeModal() {
    document.getElementById('confirmModal').style.display = 'none';
}
function submitForm() {
    document.getElementById("editBlogForm").submit();
}

document.getElementById("imageInput").addEventListener("change", function () {
    const preview = document.getElementById("previewImage");
    const file = this.files[0];
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = "block";
    }
});
</script>

</body>
</html>
