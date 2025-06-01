<?php
session_start();
require_once '../config/db.php';

$query = "SELECT b.*, m.title, u.username 
          FROM bookings b
          JOIN movies m ON b.movie_id = m.id
          JOIN users u ON b.user_id = u.id
          ORDER BY b.created_at DESC";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_id'])) {
    $verifyId = intval($_POST['verify_id']);
    $stmt = $conn->prepare("UPDATE bookings SET status='verified' WHERE id=?");
    $stmt->bind_param("i", $verifyId);
    $stmt->execute();
    header("Location: view_bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Bookings</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
    <link rel="stylesheet" href="../css/view_bookings.css?v=2">
</head>
<body>

<div class="container">
    <h1>📋 All Bookings</h1>
    <table class="bookings-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Movie</th>
                <th>Day</th>
                <th>Time</th>
                <th>Seats</th>
                <th>Total</th>
                <th>Proof</th>
                <th>Status</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['screening_day'] ?></td>
                <td><?= $row['screening_time'] ?></td>
                <td><?= $row['seats'] ?></td>
                <td><?= number_format($row['total']) ?> PKR</td>
                <td>
                    <?php if (!empty($row['payment_proof'])): ?>
                        <a href="<?= htmlspecialchars($row['payment_proof']) ?>" target="_blank">View</a>
                    <?php else: ?>N/A<?php endif; ?>
                </td>
                <td>
                    <span class="badge <?= $row['status'] === 'verified' ? 'verified' : 'unverified' ?>">
                        <?= ucfirst($row['status']) ?>
                    </span>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <?php if ($row['status'] !== 'verified'): ?>
                        <form method="POST">
                            <input type="hidden" name="verify_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="verify-btn">✅ Verify</button>
                        </form>
                    <?php else: ?>
                        <span class="verified-text">✔ Verified</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
