

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/db.php';

// Accept either ?id= or ?movie= for flexibility
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_GET['movie']) ? intval($_GET['movie']) : 0);
if (!$movie_id) {
    echo "❌ Movie not selected.";
    exit();
}

// Fetch movie info
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

// Fetch screening options
$screening_query = "SELECT screening_day, screening_time FROM screenings WHERE movie_id = ? ORDER BY screening_day, screening_time";
$screening_stmt = $conn->prepare($screening_query);
$screening_stmt->bind_param("i", $movie_id);
$screening_stmt->execute();
$screenings_result = $screening_stmt->get_result();

$unique_days = [];
$time_by_day = [];
while ($row = $screenings_result->fetch_assoc()) {
    $day = $row['screening_day'] ?? '';
    $time = $row['screening_time'] ?? '';
    if ($day && !in_array($day, $unique_days)) {
        $unique_days[] = $day;
    }
    if ($day && $time) {
        $time_by_day[$day][] = $time;
    }
}

$bookedSeats = ['B4', 'C3', 'D5']; // Example booked
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Seats | <?= htmlspecialchars($movie['title']) ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        <?php include 'css/buy_ticket.css'; ?>
    </style>
</head>
<body>
<div class="booking-container">
    <h2 class="animate__animated animate__fadeInDown"><?= htmlspecialchars($movie['title']) ?> - Book Your Seat</h2>

    <div class="card animate__animated animate__fadeIn" id="front-view">
        <div class="movie-info">
            <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="animate__animated animate__fadeInLeft">
            <div class="animate__animated animate__fadeInRight">
                <p><strong>Screen Type:</strong> <?= htmlspecialchars($movie['screen_type']) ?></p>
                <p><strong>Duration:</strong> <?= htmlspecialchars($movie['duration']) ?> </p>
                <p><strong>Language:</strong> <?= htmlspecialchars($movie['language']) ?></p>
            </div>
        </div>

        <form id="screening-form" class="animate__animated animate__fadeInUp">
            <div class="select-group">
                <div class="custom-select">
                    <label for="day">Select Day</label>
                    <select name="day" id="day" required>
                        <option value="" disabled selected>Choose a day</option>
                        <?php foreach ($unique_days as $day): ?>
                            <option value="<?= htmlspecialchars($day) ?>"><?= htmlspecialchars($day) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="select-arrow"></div>
                </div>

                <div class="custom-select">
                    <label for="time">Select Time</label>
                    <select name="time" id="time" required disabled>
                        <option value="" disabled selected>First choose a day</option>
                        <?php 
                        foreach ($time_by_day as $day => $times) {
                            foreach ($times as $time) {
                                echo '<option value="' . htmlspecialchars($time) . '" data-day="' . htmlspecialchars($day) . '" class="time-option">' . htmlspecialchars($time) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="select-arrow"></div>
                </div>
            </div>

            <button type="button" class="btn-book" onclick="showSeatSelection()">Next</button>
        </form>
    </div>

    <form action="process_booking.php" method="POST" id="back-view" class="hidden">
        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
        <input type="hidden" name="screening_day" id="hidden_day">
        <input type="hidden" name="screening_time" id="hidden_time">

        <div class="seat-legend">
            <div><span class="legend-available"></span> Available</div>
            <div><span class="legend-selected"></span> Selected</div>
            <div><span class="legend-booked"></span> Booked</div>
            <div><span style="background-color: yellow;" class="legend-box"></span> Premium</div>
            <div><span style="background-color: pink;" class="legend-box"></span> VIP</div>
        </div>

        <div class="seat-layout">
            <?php
            $rows = range('A', 'E');
            $cols = range(1, 8);
            foreach ($rows as $row) {
                echo "<div class='seat-row'>";
                foreach ($cols as $col) {
                    $seat = $row . $col;
                    $isBooked = in_array($seat, $bookedSeats);
                    $bookedClass = $isBooked ? 'booked' : '';
                    $seatTypeClass = '';
                    if ($row === 'A' || $row === 'B') {
                        $seatTypeClass = 'premium';
                    } elseif ($row === 'C') {
                        $seatTypeClass = 'vip';
                    }
                    $disabled = $isBooked ? 'disabled' : '';
                    echo "
                        <label class='seat $seatTypeClass $bookedClass'>
                            <input type='checkbox' name='seats[]' value='$seat' $disabled onchange='toggleProceed()'>
                            <span>$seat</span>
                        </label>
                    ";
                }
                echo "</div>";
            }
            ?>
        </div>

        <div class="submit-section" id="proceed-button" style="display: none;">
            <button type="submit" class="btn-book">Proceed to Payment</button>
        </div>
    </form>
</div>

<script>
document.getElementById('day').addEventListener('change', function() {
    const day = this.value;
    const timeSelect = document.getElementById('time');
    timeSelect.disabled = !day;

    const allTimeOptions = document.querySelectorAll('.time-option');
    allTimeOptions.forEach(option => {
        option.style.display = 'none';
        if (option.dataset.day === day) {
            option.style.display = 'block';
        }
    });

    timeSelect.selectedIndex = 0;
});

function showSeatSelection() {
    const day = document.getElementById("day").value;
    const time = document.getElementById("time").value;
    if (!day || !time) {
        alert("Please select both day and time.");
        return;
    }

    document.getElementById("hidden_day").value = day;
    document.getElementById("hidden_time").value = time;

    document.getElementById("front-view").classList.add("hidden");
    document.getElementById("back-view").classList.remove("hidden");
}

function toggleProceed() {
    const checkedSeats = document.querySelectorAll('input[name="seats[]"]:checked');
    document.getElementById("proceed-button").style.display = checkedSeats.length > 0 ? "block" : "none";
}
</script>
</body>
</html>