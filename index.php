<?php
// Start session if needed
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to CineWorld 🎥</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
        <h1>Welcome to CineWorld Entertainments </h1>
        <p>Book your favorite movies instantly!</p>
        <a href="register.php" class="btn">Lets Get Started !</a>
    </div>
</header>

<section class="movies-section">
    <h2>Now Showing</h2>
    <div class="movies-grid">
        <!-- Movies will be shown here dynamically later -->
        <div class="movie-card">
            <img src="assets/images/movie1.jpg" alt="Movie Title">
            <h3>Movie Title 1</h3>
        </div>
        <div class="movie-card">
            <img src="assets/images/movie2.jpg" alt="Movie Title">
            <h3>Movie Title 2</h3>
        </div>
        <!-- Add more movie cards -->
    </div>
</section>

<footer>
    <p>&copy; 2025 CineWorld. All rights reserved.</p>
</footer>

<script src="js/script.js"></script>
</body>
</html>
