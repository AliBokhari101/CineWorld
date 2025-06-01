<?php
require 'config/db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        $message = "<p class='success'>Registration successful! 🎉</p>";
    } else {
        $message = "<p class='error'>Error: " . $conn->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Cinema Booking</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Inline style for the select element only */
        .custom-select {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 25px;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") no-repeat;
            background-position: right 15px center;
            color: #333;
            font-size: 1rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
        }
        .custom-select:focus {
            outline: none;
            border-color: #e50914;
        }
    </style>
</head>
<body class="auth-body">

<div class="auth-form">
    <h2>Create Account</h2>
    <?php echo $message; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" class="auth-input" required>
        <input type="email" name="email" placeholder="Email address" class="auth-input" required>
        <input type="password" name="password" placeholder="Password" class="auth-input" required>

        <select name="role" class="custom-select" required style="padding-right: 35px;">
            <option value="" disabled selected>Select Role</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Sign Up</button>
    </form>
    <p style="color: #000;">Already have an account? <a href="login.php" style="color:#e50914;">Login!</a></p>
</div>

</body>
</html>