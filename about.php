<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us | Blogify</title>
      <link rel="stylesheet" href="assets/css/navbar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f9f9;
      color: #333;
      margin: 0;
      padding: 0;
    }

    header {
      background: linear-gradient(to right, #3498db, #22a699);
      padding: 3rem 1rem;
      text-align: center;
      color: #fff;
      animation: slideInDown 1s ease;
    }

    header h1 {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }

    header p {
      font-size: 1.1rem;
    }

    .about-container {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 1rem;
      display: flex;
      flex-direction: column;
      gap: 2rem;
      animation: fadeIn 1.2s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .about-section {
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.05);
    }

    .about-section h2 {
      color: #22a699;
      margin-bottom: 1rem;
    }

    .about-section p {
      line-height: 1.7;
      font-size: 1rem;
    }

    .footer {
      background-color: #22a699;
      color: #fff;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
    }

    @media (max-width: 768px) {
      header h1 {
        font-size: 2rem;
      }
      .about-section {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>About Blogify</h1>
    <p>Empowering voices. Sharing stories. Connecting ideas.</p>
  </header>

  <main class="about-container">
    <section class="about-section">
      <h2>Who We Are</h2>
      <p>Blogify is a platform built for writers, thinkers, and storytellers to express their ideas freely and beautifully. Our mission is to enable creativity and inspire knowledge through modern blogging experiences.</p>
    </section>

    <section class="about-section">
      <h2>Our Vision</h2>
      <p>We envision a world where every voice has the power to reach millions. Blogify brings together technology and creativity to create an inclusive space for writers of all kinds—from casual bloggers to professionals.</p>
    </section>

    <section class="about-section">
      <h2>What We Offer</h2>
      <p>• Clean and rich writing tools<br>
         • Community interaction with likes & comments<br>
         • Easy category-based filtering<br>
         • Secure and user-friendly interface
      </p>
    </section>
  </main>
<?php include 'footer.php'; ?>
</body>
</html>
