<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | CineWorld</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
    <link rel="stylesheet" href="../css/admin-style.css">
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

<section class="admin-welcome">
    <h1>Welcome, <?php echo $_SESSION['username']; ?> 👋</h1>
    <p>Manage your CineWorld from here.</p>
</section>

<section class="admin-cards">
    <div class="card">
        <h2>Add New Movie</h2>
        <p>Create a new movie listing for users.</p>
        <a href="add_movie.php" class="btn">Add Movie</a>
    </div>

    <div class="card">
        <h2>Manage Movies</h2>
        <p>Edit or delete existing movies.</p>
        <a href="view_movies.php" class="btn">Manage Movies</a>
    </div>

    <div class="card">
        <h2>View Bookings</h2>
        <p>See all user bookings.</p>
        <a href="view_bookings.php" class="btn">View Bookings</a>
    </div>
</section>



</body>
</html>
