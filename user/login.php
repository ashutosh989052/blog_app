<?php
require_once '../includes/db.php';
session_start();

$error = '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $email, $hashed);
        $stmt->fetch();

        if (password_verify($password, $hashed)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Blogify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <h2><i class="fas fa-sign-in-alt"></i> Login to Blogify</h2>
        <p class="subtitle">Access your account to share and read blogs</p>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert success">Registration successful. Please login.</div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($email) ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>
            <a href="../index.php" class="home-btn"><i class="fas fa-home"></i> Go to Home</a>

            <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>
</div>
</body>
</html>
