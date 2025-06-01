<?php
session_start();
require_once '../config/db.php';

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_movie'])) {
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $_POST['movie_id']);
    $stmt->execute();
    $stmt->close();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_movie'])) {
    $stmt = $conn->prepare("UPDATE movies SET title=?, screen_type=?, status=?, show_date=?, show_time=?, trailer_url=?, poster_url=? WHERE id=?");
    $stmt->bind_param(
        "sssssssi",
        $_POST['title'],
        $_POST['screen_type'],
        $_POST['status'],
        $_POST['show_date'],
        $_POST['show_time'],
        $_POST['trailer_url'],
        $_POST['poster_url'],
        $_POST['movie_id']
    );
    $stmt->execute();
    $stmt->close();
}

// Search logic
$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $stmt = $conn->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY show_date, show_time");
    $term = "%$search%";
    $stmt->bind_param("s", $term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM movies ORDER BY show_date, show_time");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Movies</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
  <link rel="stylesheet" href="../css/view_movies.css?v=2">
  
</head>

<body class="admin-page">
<nav class="admin-navbar">
    <div class="logo">CineWorld Admin 🎬</div>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="add_movie.php">Add Movie</a></li>
        <li><a href="view_movies.php">View Movies</a></li>
        <li><a href="view_bookings.php">View Bookings</a></li>
        <li><a href="admin_view_contacts.php">View Messages</a></li>
        <li><a href="../login.php">Logout</a></li>
    </ul>
</nav>
<header class="admin-header">
  <h1>🎬 Manage Movies</h1>
</header>

<section class="search-bar">
  <form method="POST">
    <input type="text" name="search" placeholder="Search for a movie..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">🔍 Search</button>
  </form>
</section>

<div class="movie-container-box">
  <?php if ($result->num_rows > 0): ?>
    <?php while($movie = $result->fetch_assoc()): ?>
      <div class="movie-card">
        <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="Poster">
        <div class="movie-info">
          <?php if ($isAdmin && isset($_GET['edit']) && $_GET['edit'] == $movie['id']): ?>
            <form method="POST" class="edit-form">
              <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
              <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required>
              <input type="text" name="screen_type" value="<?= $movie['screen_type'] ?>" required>
              <input type="text" name="status" value="<?= $movie['status'] ?>" required>
              <input type="date" name="show_date" value="<?= $movie['show_date'] ?>" required>
              <input type="time" name="show_time" value="<?= $movie['show_time'] ?>" required>
              <input type="url" name="trailer_url" value="<?= $movie['trailer_url'] ?>" required>
              <input type="url" name="poster_url" value="<?= $movie['poster_url'] ?>" required>
              <div class="action-buttons">
                <button type="submit" name="update_movie" class="btn edit">💾 Save</button>
                <a href="view_movies.php" class="btn cancel">❌ Cancel</a>
              </div>
            </form>
          <?php else: ?>
            <h3><?= htmlspecialchars($movie['title']) ?></h3>
            <p><strong>Type:</strong> <?= $movie['screen_type'] ?></p>
            <p><strong>Status:</strong> <?= $movie['status'] ?></p>
            <p><strong>Date:</strong> <?= $movie['show_date'] ?></p>
            <p><strong>Time:</strong> <?= date('h:i A', strtotime($movie['show_time'])) ?></p>
            <a href="<?= htmlspecialchars($movie['trailer_url']) ?>" target="_blank" class="trailer-btn">▶ Watch Trailer</a>
            <?php if ($isAdmin): ?>
              <div class="admin-actions">
                <form method="POST" onsubmit="return confirm('Delete this movie?')">
                  <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                  <button type="submit" name="delete_movie" class="btn delete">🗑️ Remove</button>
                </form>
                <a href="view_movies.php?edit=<?= $movie['id'] ?>" class="btn edit">✏️ Edit</a>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="no-movies">No movies available.</p>
  <?php endif; ?>
</div>



</body>
</html>
