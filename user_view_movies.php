<?php
session_start();
require_once 'config/db.php';

// Fetch movies
$sql = "SELECT * FROM movies ORDER BY show_date, show_time";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Now Showing | CineWorld</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
  <link rel="stylesheet" href="css/user_view_movies.css">
  <link rel="stylesheet" href="css/home.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<header class="user-header">
  <h1>🎬<i> Exclusives</i></h1>
</header>
<section class="search-bar">
  <form method="POST">
    <input type="text" name="search" placeholder="Search movies..." value="<?= htmlspecialchars($search ?? '') ?>">
    <button type="submit">🔍</button>
  </form>
</section>

<section class="movie-grid">
  <?php while ($movie = $result->fetch_assoc()): ?>
    <div class="movie-tile">
      <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="Poster">
      <div class="movie-overlay">
        <h2><?= htmlspecialchars($movie['title']) ?></h2>
        <p><?= $movie['status'] ?> | <?= $movie['show_date'] ?> at <?= date('h:i A', strtotime($movie['show_time'])) ?></p>
        <div class="movie-buttons">
          <?php if (strtolower($movie['status']) !== 'upcoming'): ?>
            <a href="buy_ticket.php?movie=<?php echo $movie['id']; ?>" class="btn buy-ticket">Buy Ticket</a>
          <?php endif; ?>
          <a href="<?= htmlspecialchars($movie['trailer_url']) ?>" target="_blank" class="btn trailer">▶ Trailer</a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</section>
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
