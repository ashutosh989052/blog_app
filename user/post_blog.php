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

// Fetch all categories
$category_result = $conn->query("SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $category_id = intval($_POST["category"]);
    $content = $_POST["content"];
    $user_id = $_SESSION['user_id'];

    // Handle image upload safely
    $image_path = "";
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $image_path = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            echo "<p style='color:red;'>Image upload failed.</p>";
            $image_path = ""; // reset path
        }
    }

    $stmt = $conn->prepare("INSERT INTO blogs (user_id, category_id, title, content, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $category_id, $title, $content, $image_path);

    if ($stmt->execute()) {
        $message = "Blog posted successfully!";
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
      <nav class="navbar">
    <div class="logo">Blogify</div>
    <ul class="nav-links">
      <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
      <li><a href="post_blog.php "class="active"><i class="fas fa-pen"></i> Post Blog</a></li>
      <li><a href="my_blogs.php"><i class="fas fa-user-edit"></i> My Blogs</a></li>
      <li><a href="../index.php"><i class="fas fa-globe"></i> Explore</a></li>
      <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <div class="form-container">
    <h2>Post a New Blog</h2>
    <p style="color:green;"><?php echo $message; ?></p>

    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="title" required placeholder="Blog Title"><br>

      <select name="category" required>
        <option value="">-- Select Category --</option>
        <?php while($cat = $category_result->fetch_assoc()): ?>
          <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
        <?php endwhile; ?>
      </select><br>

      <textarea id="content" name="content"></textarea><br>

      <label>Upload Blog Cover Image:</label><br>
      <input type="file" name="image"><br><br>

      <button type="submit">Post Blog</button>
    </form>
  </div>

</body>
</html>
