<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";
$redirect = false;

// Fetch all categories
$category_result = $conn->query("SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $category_id = intval($_POST["category"]);
    $content = $_POST["content"];
    $user_id = $_SESSION['user_id'];
    $image_path = "";

    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $image_path = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            $message = "<p style='color:red;'>Image upload failed.</p>";
            $image_path = "";
        }
    }

    $stmt = $conn->prepare("INSERT INTO blogs (user_id, category_id, title, content, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $category_id, $title, $content, $image_path);

    if ($stmt->execute()) {
        $message = "Blog posted successfully!";
        $redirect = true;
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Post Blog</title>
  <link rel="stylesheet" href="../assets/css/post_blog.css">
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
  <h2>Post a New Blog</h2>
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

  <form id="postBlogForm" method="POST" enctype="multipart/form-data">
    <input type="text" name="title" required placeholder="Blog Title"><br>

    <select name="category" required>
      <option value="">-- Select Category --</option>
      <?php while($cat = $category_result->fetch_assoc()): ?>
        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
      <?php endwhile; ?>
    </select><br>

    <textarea id="content" name="content"></textarea><br>

    <label>Upload Blog Cover Image:</label><br>
    <input type="file" name="image" id="imageInput"><br>
    <div id="previewContainer">
      <img id="previewImage" src="#" style="display:none; max-width: 100%; margin-top: 10px; border-radius: 10px;">
    </div><br>

    <button type="button" onclick="showModal()">Post Blog</button>
  </form>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal">
  <div class="modal-content">
    <p>Are you sure you want to post this blog?</p>
    <button onclick="submitForm()">Yes, Post</button>
    <button onclick="closeModal()">Cancel</button>
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
  document.getElementById('postBlogForm').submit();
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
