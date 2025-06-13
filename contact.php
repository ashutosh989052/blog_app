<?php
require_once 'includes/db.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if (!empty($name) && !empty($email) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $message);
            if ($stmt->execute()) {
                $success = "Your message has been sent successfully!";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        } else {
            $error = "Invalid email address.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us | Blogify</title>
      <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet"/>
  <style>
    * { box-sizing: border-box; }

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: linear-gradient(to right, #fdfbfb, #ebedee);
      color: #333;
    }

    header {
      background: linear-gradient(to right, #22a699, #38b6ff);
      padding: 2.5rem 1rem;
      text-align: center;
      color: white;
      animation: fadeSlideDown 1s ease-in-out;
    }

    @keyframes fadeSlideDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .container {
      max-width: 900px;
      margin: 2rem auto;
      padding: 1rem;
    }

    .contact-form {
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      animation: fadeIn 1.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .contact-form h2 {
      color: #22a699;
      margin-bottom: 1rem;
      font-size: 1.6rem;
    }

    .contact-form label {
      font-weight: 500;
      display: block;
      margin-top: 1.2rem;
      margin-bottom: 0.5rem;
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    .contact-form input:focus,
    .contact-form textarea:focus {
      border-color: #22a699;
      outline: none;
    }

    .contact-form button {
      margin-top: 1rem;
      background: linear-gradient(to right, #22a699, #38b6ff);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border-radius: 6px;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .contact-form button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    .message-box {
      margin-top: 1rem;
      padding: 10px 15px;
      border-radius: 6px;
      font-weight: 500;
    }

    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }

    .contact-info {
      margin-top: 2rem;
      text-align: center;
      color: #555;
    }

    .contact-info p {
      margin: 0.5rem 0;
    }

    .contact-info i {
      color: #22a699;
      margin-right: 6px;
    }

    .footer {
      background-color: #22a699;
      color: #fff;
      text-align: center;
      padding: 1rem;
      margin-top: 3rem;
    }

    @media screen and (max-width: 768px) {
      header h1 { font-size: 1.8rem; }
      .contact-form { padding: 1.2rem; }
    }

    @media screen and (max-width: 480px) {
      .contact-form h2 { font-size: 1.4rem; }
    }
  </style>
</head>
<body>

  <header>
    <h1>Contact Us</h1>
    <p>We'd love to hear from you!</p>
  </header>

  <div class="container">
    <form class="contact-form" method="POST" action="" onsubmit="return validateForm();" data-aos="zoom-in">
      <h2>Send a Message</h2>

      <?php if (!empty($success)): ?>
        <div class="message-box success"><?= htmlspecialchars($success) ?></div>
      <?php elseif (!empty($error)): ?>
        <div class="message-box error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <label for="name">Name</label>
      <input type="text" id="name" name="name" placeholder="Your full name" required />

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" required />

      <label for="message">Message</label>
      <textarea id="message" name="message" rows="5" placeholder="Write your message..." required></textarea>

      <button type="submit">Send</button>
    </form>

    <div class="contact-info" data-aos="fade-up">
      <p><i class="fas fa-envelope"></i> contact@blogify.com</p>
      <p><i class="fas fa-map-marker-alt"></i> Mumbai, Maharashtra, India</p>
    </div>
  </div>

  <footer class="footer">
    &copy; <?= date("Y") ?> Blogify. All Rights Reserved.
  </footer>

  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>AOS.init();</script>

  <script>
    function validateForm() {
      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const message = document.getElementById("message").value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (name.length < 3) {
        alert("Name must be at least 3 characters.");
        return false;
      }
      if (!emailRegex.test(email)) {
        alert("Enter a valid email address.");
        return false;
      }
      if (message.length < 10) {
        alert("Message must be at least 10 characters.");
        return false;
      }
      return true;
    }
  </script>
  <?php include 'footer.php'; ?>
</body>
</html>
