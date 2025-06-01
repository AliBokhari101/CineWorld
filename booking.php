<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
?>
<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Ticket</title>
    <style>
        :root {
            --primary: #011936;
            --secondary: #F9DC5C;
            --accent: #06D6A0;
            --danger: #ED254E;
            --dark-bg: #001124;
            --light-text: #D8CBC7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--primary);
            color: white;
            line-height: 1.6;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--dark-bg);
            padding: 20px 50px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--secondary);
        }

        .nav-links {
            list-style: none;
            display: flex;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--secondary);
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            background: var(--dark-bg);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        h1 {
            color: var(--secondary);
            text-align: center;
            margin-bottom: 30px;
        }

        .booking {
            background: var(--primary);
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid var(--accent);
            border-radius: 8px;
        }

        .booking p {
            margin: 8px 0;
        }

        .status {
            font-weight: bold;
            color: var(--danger);
        }

        .download-btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--accent);
            color: var(--primary);
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: background 0.3s ease;
            margin-top: 10px;
        }

        .download-btn:hover {
            background: #04b489;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">CineMagic</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="user_view_movies.php">Movies</a></li>
            <li><a href="bookings.php">My Bookings</a></li>
            <li><a href="login.php">Logout</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>Your Bookings</h1>
        <?php
include(__DIR__ . '/config/db.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT b.*, m.title FROM bookings b
        JOIN movies m ON b.movie_id = m.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='booking'>";
        echo "<p><strong>Movie:</strong> {$row['title']}</p>";
        echo "<p><strong>Seats:</strong> {$row['seats']}</p>";
        echo "<p><strong>Show Date:</strong> {$row['screening_day']}</p>";
        echo "<p><strong>Time:</strong> {$row['screening_time']}</p>";
        echo "<p><strong>Status:</strong> <span class='status'>{$row['status']}</span></p>";

        // Normalize status before comparison
        $status = strtolower(trim($row['status']));
        if ($status === 'approved' || $status === 'verified') {
            echo "<a href='download_ticket.php?booking_id={$row['id']}' class='download-btn'>Download Ticket</a>";
        }

        echo "</div>";
    }
} else {
    echo "<p style='text-align:center;'>No bookings found.</p>";
}
?>
    </div>
</body>
</html>
