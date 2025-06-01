<?php
// Database settings
$host = "localhost";      // Server host (keep as localhost)
$user = "root";            // XAMPP default MySQL user
$pass = "";                // XAMPP default password is empty
$dbname = "cinema_db";     // The database you will create (we'll create it now)

// Create connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
