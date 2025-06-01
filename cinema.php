<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cinemas | CineWorld</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
  <link rel="stylesheet" href="css/user_view_movies.css">
  <link rel="stylesheet" href="css/home.css">
  <style>
    .cinema-header {
      text-align: center;
      color: #fff;
      background-color:#e50914  ;
      padding: 0px 10px;
      font-size: 1.7rem;
    }

    .cinema-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 40px;
      padding: 0px 20px;
      margin-top:30px;
    }

    .cinema-card {
      background-color: #fff;
      color: #011936;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
      width: 400px;
      transition: transform 0.3s ease;
    }

    .cinema-card:hover {
      transform: scale(1.00);
    }

    .cinema-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .cinema-info {
      padding: 20px;
    }

    .cinema-info h3 {
      margin: 0 0 10px;
    }

    .cinema-info p {
      margin: 6px 0;
      font-size: 0.95rem;
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

<header class="cinema-header">
  <h1>🎥<i>Cinemas</i></h1>
</header>

<section class="cinema-grid">
  <!-- Cinema 1 -->
  <div class="cinema-card">
    <img src="images/cinema1.jpg" alt="Cinema 1">
    <div class="cinema-info">
      <h3>CineWorld Downtown</h3>
      <p>📍 123 Main Street, Cityville</p>
      <p style="color:blue;">📞+996 0892 1234</p=>
      <p>Modern seating, 4K screens, Dolby Atmos sound</p>
    </div>
  </div>

  <!-- Cinema 2 -->
  <div class="cinema-card">
    <img src="images/cinema2.jpg" alt="Cinema 2">
    <div class="cinema-info">
      <h3>CineWorld Express</h3>
      <p>📍 45 Avenue Park, Townside</p>
      <p style="color:blue;">📞+996 8049 2804</p=>
      <p>Fast access cinema with premium halls</p>
    </div>
  </div>

  <!-- Cinema 3 -->
  <div class="cinema-card">
    <img src="images/cinema3.jpg" alt="Cinema 3">
    <div class="cinema-info">
      <h3>CineWorld Grand</h3>
      <p>📍 89 River Road, Metro City</p>
      <p style="color:blue;">📞+996 8945 7632</p=>
      <p>Luxury lounges, 3D & IMAX screens</p>
    </div>
  </div>
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
