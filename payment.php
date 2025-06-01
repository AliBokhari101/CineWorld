<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process form submission only when 'Confirm & Pay' button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    if (isset($_POST['movie_id'], $_POST['seats'], $_POST['total'], $_POST['screening_day'], $_POST['screening_time'])) {
        $user_id = $_SESSION['user_id'];
        $movie_id = intval($_POST['movie_id']);
        $selected_seats = $_POST['seats'];
        $total = intval($_POST['total']);
        $seat_string = implode(", ", $selected_seats);
        $screening_day = $_POST['screening_day'];
        $screening_time = $_POST['screening_time'];
        $status = 'unverified';
        $created_at = date('Y-m-d H:i:s');

        // Determine payment proof
        $payment_proof = '';
        if (!empty($_POST['easypaisa_receipt_url'])) {
            $payment_proof = $_POST['easypaisa_receipt_url'];
        } elseif (!empty($_POST['jazzcash_receipt_url'])) {
            $payment_proof = $_POST['jazzcash_receipt_url'];
        } elseif (!empty($_POST['card_name'])) {
            $payment_proof = "Card Payment";
        }

        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, movie_id, screening_day, screening_time, seats, total, payment_proof, status, created_at)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssisss", $user_id, $movie_id, $screening_day, $screening_time, $seat_string, $total, $payment_proof, $status, $created_at);

        if ($stmt->execute()) {
    // Get the last inserted booking ID
    $booking_id = $stmt->insert_id;

    // Redirect to download_ticket.php with the booking ID
    header("Location: download_ticket.php?booking_id=" . $booking_id);
    exit();
} else {
    echo "<div style='padding: 20px; background: #ED254E; color: white;'>❌ Error: " . $stmt->error . "</div>";
}

        $stmt->close();
    } else {
        echo "<div style='padding: 20px; background: #ED254E; color: white;'>❌ Missing booking data.</div>";
    }
}

// Ensure required POST data (for displaying movie/payment form)
if (!isset($_POST['movie_id'], $_POST['seats'], $_POST['total'], $_POST['screening_day'], $_POST['screening_time'])) {
    echo "❌ Invalid access. Required information is missing.";
    exit();
}

$movie_id = intval($_POST['movie_id']);
$selected_seats = $_POST['seats'];
$total = intval($_POST['total']);
$screening_day = $_POST['screening_day'];
$screening_time = $_POST['screening_time'];

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

$challan_number = 'CH-' . strtoupper(substr(uniqid(), -8));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - <?= htmlspecialchars($movie['title']) ?></title>
  <link rel="stylesheet" href="css/payment.css">
  <script src="https://widget.cloudinary.com/v2.0/global/all.js" type="text/javascript"></script>
</head>
<body>
<div class="payment-container">
  <!-- Movie Panel -->
  <div class="panel movie-panel">
    <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="Movie Poster" class="movie-poster">
    <h2><?= htmlspecialchars($movie['title']) ?></h2>
    <p><strong>Screen Type:</strong> <?= htmlspecialchars($movie['screen_type']) ?></p>
    <p><strong>Duration:</strong> <?= htmlspecialchars($movie['duration']) ?></p>
    <p><strong>Language:</strong> <?= htmlspecialchars($movie['language']) ?></p>
  </div>

  <!-- Payment Panel -->
  <div class="panel payment-panel">
    <h3>Select Payment Method</h3>
    <div class="methods">
      <button class="method active" data-method="card">💳 Visa/Mastercard</button>
      <button class="method" data-method="easypaisa">📱 Easypaisa</button>
      <button class="method" data-method="jazzcash">📲 JazzCash</button>
    </div>

    <form method="POST">
      <input type="hidden" name="confirm_payment" value="1">
      <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
      <input type="hidden" name="screening_day" value="<?= htmlspecialchars($screening_day) ?>">
      <input type="hidden" name="screening_time" value="<?= htmlspecialchars($screening_time) ?>">
      <?php foreach ($selected_seats as $seat): ?>
        <input type="hidden" name="seats[]" value="<?= htmlspecialchars($seat) ?>">
      <?php endforeach; ?>
      <input type="hidden" name="total" value="<?= $total ?>">

      <!-- Card Panel -->
      <div class="method-panel" id="card-panel">
        <input type="text" name="card_name" placeholder="Name on Card">
        <input type="text" name="card_number" placeholder="Card Number">
        <div class="row">
          <input type="text" name="expiry" placeholder="MM/YY">
          <input type="text" name="cvv" placeholder="CVV">
        </div>
      </div>

      <!-- Easypaisa Panel -->
      <div class="method-panel hidden" id="easypaisa-panel">
        <div class="challan-number">Challan Number: <?= $challan_number ?></div>
        <input type="hidden" name="easypaisa_challan" value="<?= $challan_number ?>">
        <p>Pay to this challan number & upload proof:</p>
        <button type="button" class="upload-btn" id="easypaisa-upload">📤 Upload Receipt</button>
        <input type="hidden" name="easypaisa_receipt_url" id="easypaisa_url">
        <div id="easypaisa-preview"></div>
      </div>

      <!-- JazzCash Panel -->
      <div class="method-panel hidden" id="jazzcash-panel">
        <div class="challan-number">Challan Number: <?= $challan_number ?></div>
        <input type="hidden" name="jazzcash_challan" value="<?= $challan_number ?>">
        <p>Pay to this challan number & upload proof:</p>
        <button type="button" class="upload-btn" id="jazzcash-upload">📤 Upload Receipt</button>
        <input type="hidden" name="jazzcash_receipt_url" id="jazzcash_url">
        <div id="jazzcash-preview"></div>
      </div>
  </div>

  <!-- Summary Panel -->
  <div class="panel summary-panel">
    <div class="summary">
      <h3>Booking Summary</h3>
      <p><strong>Movie:</strong> <?= htmlspecialchars($movie['title']) ?></p>
      <p><strong>Seats:</strong> <?= implode(", ", array_map('htmlspecialchars', $selected_seats)) ?></p>
      <p><strong>Date:</strong> <?= htmlspecialchars($screening_day) ?></p>
      <p><strong>Time:</strong> <?= htmlspecialchars($screening_time) ?></p>
      <p><strong>Total:</strong> <?= $total ?> PKR</p>
    </div>

    <button type="submit" class="confirm-btn">Confirm & Pay</button>
    </form>
  </div>
</div>

<script>
  const methods = document.querySelectorAll('.method');
  const panels = {
    card: document.getElementById('card-panel'),
    easypaisa: document.getElementById('easypaisa-panel'),
    jazzcash: document.getElementById('jazzcash-panel'),
  };

  methods.forEach(button => {
    button.addEventListener('click', () => {
      methods.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      Object.values(panels).forEach(p => p.classList.add('hidden'));
      panels[button.dataset.method].classList.remove('hidden');
    });
  });

  const cloudName = "debq5gzej";
  const uploadPreset = "unsigned_preset";

  function setupCloudinaryUpload(buttonId, inputId, previewId) {
    document.getElementById(buttonId).addEventListener("click", function () {
      cloudinary.openUploadWidget({
        cloudName: cloudName,
        uploadPreset: uploadPreset,
        sources: ["local", "camera"],
        multiple: false,
        folder: "payment_receipts",
        cropping: false
      }, (error, result) => {
        if (!error && result && result.event === "success") {
          const secureUrl = result.info.secure_url;
          document.getElementById(inputId).value = secureUrl;
          document.getElementById(previewId).innerHTML =
            `<img src="${secureUrl}" alt="Receipt" style="max-width: 100%; max-height: 200px;">`;
        }
      });
    });
  }

  setupCloudinaryUpload("easypaisa-upload", "easypaisa_url", "easypaisa-preview");
  setupCloudinaryUpload("jazzcash-upload", "jazzcash_url", "jazzcash-preview");
</script>
</body>
</html>
