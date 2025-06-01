<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize movie data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $poster_url = trim($_POST['poster_url']);
    $trailer = trim($_POST['trailer']);
    $duration = trim($_POST['duration']);
    $language = trim($_POST['language']);
    $genre = trim($_POST['genre']);
    $screen_type = trim($_POST['screen_type']);
    $status = trim($_POST['status']);

    // Collect screening data (arrays for multiple screenings)
    $screening_dates = $_POST['screening_date'];
    $screening_days = $_POST['screening_day'];
    $screening_times = $_POST['screening_time'];

    // Validate status
    $allowed_status = ['now_showing', 'upcoming'];
    if (!in_array($status, $allowed_status)) {
        $error = "❌ Invalid movie status selected.";
    } else {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert movie
            $stmt = $conn->prepare("INSERT INTO movies (title, description, poster_url, trailer_url, duration, language, genre, screen_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $title, $description, $poster_url, $trailer, $duration, $language, $genre, $screen_type, $status);
            
            if (!$stmt->execute()) {
                throw new Exception("❌ Failed to add movie.");
            }
            
            $movie_id = $conn->insert_id;
            $stmt->close();

            // Insert screenings
            $stmt2 = $conn->prepare("INSERT INTO screenings (movie_id, screening_date, screening_day, screening_time) VALUES (?, ?, ?, ?)");
            
            $screening_count = 0;
            for ($i = 0; $i < count($screening_dates); $i++) {
                if (!empty($screening_dates[$i]) && !empty($screening_times[$i])) {
                    $stmt2->bind_param("isss", $movie_id, $screening_dates[$i], $screening_days[$i], $screening_times[$i]);
                    if (!$stmt2->execute()) {
                        throw new Exception("❌ Failed to add one or more screenings.");
                    }
                    $screening_count++;
                }
            }
            
            if ($screening_count == 0) {
                throw new Exception("❌ At least one screening is required.");
            }
            
            $stmt2->close();
            
            // Commit transaction
            $conn->commit();
            $success = "✅ Movie and $screening_count screening(s) added successfully!";
            
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Movie | Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin-style.css?v=3">
    <script src="https://widget.cloudinary.com/v2.0/global/all.js" type="text/javascript"></script>
    <style>
        .screening-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            position: relative;
        }
        .remove-screening {
            position: absolute;
            right: 15px;
            top: 15px;
            color: #dc3545;
            cursor: pointer;
            font-size: 1.2rem;
        }
        #add-screening {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            cursor: pointer;
        }
        #add-screening:hover {
            background: #218838;
        }
        .screening-fields {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        @media (max-width: 768px) {
            .screening-fields {
                grid-template-columns: 1fr;
            }
        }
    </style>
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

<div class="admin-form-container">
    <h1>Add New Movie 🎬</h1>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" id="movie-form">
        <div class="form-group">
            <input type="text" name="title" placeholder="Movie Title" required>
        </div>
        
        <div class="form-group">
            <textarea name="description" placeholder="Movie Description" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <input type="hidden" name="poster_url" id="poster_url">
            <button type="button" id="upload_widget" class="upload-btn">
                <i class="fas fa-image"></i> Upload Poster
            </button>
            <div id="poster_preview"></div>
        </div>

        <div class="form-group">
            <input type="url" name="trailer" placeholder="YouTube Trailer URL" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <input type="text" name="duration" placeholder="Duration (e.g., 2h 10m)" required>
            </div>
            <div class="form-group">
                <input type="text" name="language" placeholder="Language (e.g., English)" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <input type="text" name="genre" placeholder="Genre (e.g., Action, Drama)" required>
            </div>
            <div class="form-group">
                <select name="screen_type" required>
                    <option value="" disabled selected>Select Screen Type</option>
                    <option value="2D">2D</option>
                    <option value="3D">3D</option>
                    <option value="IMAX">IMAX</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="status">Movie Status:</label>
            <select name="status" id="status" required>
                <option value="now_showing">Now Showing</option>
                <option value="upcoming">Upcoming</option>
            </select>
        </div>

        <h3>Screenings <button type="button" id="add-screening"><i class="fas fa-plus"></i> Add Screening</button></h3>
        
        <div id="screenings-container">
            <!-- Initial screening field -->
            <div class="screening-container">
                <span class="remove-screening" onclick="removeScreening(this)"><i class="fas fa-times"></i></span>
                <div class="screening-fields">
                    <div>
                        <label>Screening Date</label>
                        <input type="date" name="screening_date[]" required onchange="updateDay(this)">
                    </div>
                    <div>
                        <label>Screening Day</label>
                        <input type="text" name="screening_day[]" placeholder="e.g., Friday" required>
                    </div>
                    <div>
                        <label>Screening Time</label>
                        <input type="time" name="screening_time[]" required>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="submit-btn">Add Movie</button>
    </form>
</div>

<script>
    // Cloudinary upload widget
    const myWidget = cloudinary.createUploadWidget({
        cloudName: 'debq5gzej',
        uploadPreset: 'unsigned_preset'
    }, (error, result) => {
        if (!error && result && result.event === "success") {
            document.getElementById("poster_url").value = result.info.secure_url;
            document.getElementById("poster_preview").innerHTML = `
                <div class="poster-preview">
                    <img src="${result.info.secure_url}" alt="Poster Preview">
                    <p>Poster uploaded successfully!</p>
                </div>
            `;
        }
    });

    document.getElementById("upload_widget").addEventListener("click", function() {
        myWidget.open();
    }, false);

    // Add screening fields
    document.getElementById('add-screening').addEventListener('click', function() {
        const container = document.getElementById('screenings-container');
        const newScreening = document.createElement('div');
        newScreening.className = 'screening-container';
        newScreening.innerHTML = `
            <span class="remove-screening" onclick="removeScreening(this)"><i class="fas fa-times"></i></span>
            <div class="screening-fields">
                <div>
                    <label>Screening Date</label>
                    <input type="date" name="screening_date[]" required onchange="updateDay(this)">
                </div>
                <div>
                    <label>Screening Day</label>
                    <input type="text" name="screening_day[]" placeholder="e.g., Friday" required>
                </div>
                <div>
                    <label>Screening Time</label>
                    <input type="time" name="screening_time[]" required>
                </div>
            </div>
        `;
        container.appendChild(newScreening);
    });

    // Remove screening fields
    function removeScreening(element) {
        const screenings = document.querySelectorAll('.screening-container');
        if (screenings.length > 1) {
            element.closest('.screening-container').remove();
        } else {
            alert('You must have at least one screening.');
        }
    }

    // Auto-fill day when date is selected
    function updateDay(dateInput) {
        const date = new Date(dateInput.value);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const dayName = days[date.getDay()];
        
        const container = dateInput.closest('.screening-container');
        const dayInput = container.querySelector('input[name="screening_day[]"]');
        dayInput.value = dayName;
    }
</script>
</body>
</html>