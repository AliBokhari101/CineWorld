<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !isset($_POST['movie_id'], $_POST['screening_day'], $_POST['screening_time'], $_POST['seats'])
) {
    echo "❌ Invalid access.";
    exit();
}

$movie_id = intval($_POST['movie_id']);
$screening_day = $_POST['screening_day'];
$screening_time = $_POST['screening_time'];
$selected_seats = $_POST['seats'];

require_once 'config/db.php';

// Get movie details
$query = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();
if (!$movie) {
    echo "❌ Movie not found.";
    exit();
}

// Pricing
function getSeatPrice($seat) {
    $row = substr($seat, 0, 1);
    if (in_array($row, ['A', 'B'])) return 600; // Premium
    if ($row === 'C') return 900; // VIP
    return 500; // Standard
}

$total = 0;
foreach ($selected_seats as $seat) {
    $total += getSeatPrice($seat);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Summary</title>
    
    <link rel="stylesheet" href="css/process_booking.css">
</head>
<body>
<div class="summary-container">
    <h2>Booking Summary for <?= htmlspecialchars($movie['title']) ?></h2>
    <p><strong>Screening Day:</strong> <?= htmlspecialchars($screening_day) ?></p>
    <p><strong>Screening Time:</strong> <?= htmlspecialchars($screening_time) ?></p>
    <p><strong>Selected Seats:</strong> <?= implode(", ", $selected_seats) ?></p>
    <p><strong>Total Bill:</strong> PKR <?= number_format($total) ?></p>

    <form action="payment.php" method="POST">
        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
        <input type="hidden" name="screening_day" value="<?= htmlspecialchars($screening_day) ?>">
        <input type="hidden" name="screening_time" value="<?= htmlspecialchars($screening_time) ?>">
        <?php foreach ($selected_seats as $seat): ?>
            <input type="hidden" name="seats[]" value="<?= htmlspecialchars($seat) ?>">
        <?php endforeach; ?>
        <input type="hidden" name="total" value="<?= $total ?>">
        <button type="submit" class="btn-proceed">Proceed to Payment</button>
    </form>
</div>
</body>
</html>
