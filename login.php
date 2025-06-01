<?php
session_start();
include 'config/db.php';

$message = ''; // ✅ Initialize message to avoid undefined variable warning

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $email_db, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['user_email'] = $email_db;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role == 'admin') {
                header("Location: admin/dashboard.php");
                exit();
            } else {
                header("Location: home.php");
                exit();
            }
        } else {
            $message = "<p class='error'>Incorrect password.</p>";
        }
    } else {
        $message = "<p class='error'>No account found with that email.</p>";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Cinema Booking</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-body">

<div class="auth-form">
    <h2>Login</h2>

    <!-- ✅ Safe message display -->
    <?php if (!empty($message)) {
        echo $message;
    } ?>

    <div class="loginauth">
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
    </div>
    <p>Don't have an account? <a href="register.php" style="color:#e50914;">Sign Up here</a></p>
</div>

</body>
</html>
