<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | CineWorld</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
    <link rel="stylesheet" href="css/home.css?v=<?= filemtime('css/home.css') ?>">
    <style>
        .contact-section {
            max-width: 800px;
            margin: 40px auto;
            background-color: var(--dark-bg);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        .contact-section h2 {
            color: var(--secondary);
            margin-bottom: 25px;
            font-size: 2rem;
            text-align: center;
        }
        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .contact-form input,
        .contact-form textarea {
            padding: 12px 15px;
            border-radius: 6px;
            border: none;
            background-color: var(--primary);
            color: white;
        }
        .contact-form textarea {
            min-height: 120px;
        }
        .contact-form button {
            align-self: flex-start;
            padding: 10px 25px;
            background-color: var(--accent);
            color: var(--primary);
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .contact-form button:hover {
            background-color: #04b489;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="logo">CineWorld 🎥</div>
    <ul class="nav-links">
        <li><a href="home.php">Home</a></li>
        <li><a href="user_view_movies.php">Movies</a></li>
        <li><a href="cinema.php">Cinema</a></li>
        <li><a href="booking.php">My Bookings</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="login.php" onclick="confirmLogout()">Logout</a></li>
    </ul>
</nav>
<?php if (isset($_GET['status'])): ?>
  <div class="alert-message" style="text-align:center; padding: 10px; color: white; margin-top: 20px;
       background-color: <?= $_GET['status'] === 'success' ? '#28a745' : '#dc3545'; ?>;">
    <?= $_GET['status'] === 'success' ? '✅ Email sent successfully!' : '❌ Something went wrong. Please try again.' ?>
  </div>
<?php endif; ?>

<!-- Contact Section -->
<section class="contact-section">
    <h2>📩 Contact Us</h2>
    <p style="color: var(--light-text); text-align: center; margin-bottom: 15px;">
        For urgent inquiries, call our CineWorld Helpline: <strong style="color: var(--accent);">+1-800-555-1234</strong>

    </p>
    <p style="color: var(--light-text); text-align: center; margin-bottom: 15px;">or email us,</p>
    <form class="contact-form" method="POST" action="contact_handler.php">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email Address" required>
        <textarea name="message" placeholder="Your Message..." required></textarea>
        <button type="submit">Send Message</button>
    </form>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2025 CineWorld. All rights reserved.</p>
</footer>

<script>
function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}
</script>
</body>
</html>
