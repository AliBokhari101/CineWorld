<?php
session_start();
require_once '../config/db.php';

$query = "SELECT * FROM contact_messages ORDER BY submitted_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Messages | Admin</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
  <link rel="stylesheet" href="../css/admin_view_contacts.css">
</head>
<body>

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

<div class="admin-view-contacts-container">
  <h1>📬 Contact Messages</h1>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Message</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td data-label="Name"><?= htmlspecialchars($row['name']) ?></td>
          <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
          <td data-label="Message"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
          <td data-label="Date"><?= $row['submitted_at'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>


</body>
</html>
