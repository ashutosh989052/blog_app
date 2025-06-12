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

        
         echo "Entered: $password<br>Stored: $hashed<br>";

        if (password_verify($password, $hashed)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password incorrect.";
        }
    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
<div class="auth-container">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="error-box"><p><?= $error ?></p></div>
    <?php elseif (isset($_GET['success'])): ?>
        <div class="success-box"><p>Registration successful. Please log in.</p></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($email ?? '') ?>">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </form>
</div>
</body>
</html>
