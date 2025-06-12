<?php
require_once '../includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $errors[] = "Email already registered.";
    }

    // Insert user
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hash);
        $stmt->execute();
        header("Location: login.php?success=registered");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
<div class="auth-container">
    <h2>Create Account</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): echo "<p>$e</p>"; endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars($username ?? '') ?>">
        <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($email ?? '') ?>">
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</div>
</body>
</html>
