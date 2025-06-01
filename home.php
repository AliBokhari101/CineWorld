<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'config/db.php';

$username = $_SESSION['username'];

// Fetch Movies
$nowShowingQuery = "SELECT * FROM movies WHERE status = 'now_showing'";
$nowShowingResult = mysqli_query($conn, $nowShowingQuery);
$upcomingQuery = "SELECT * FROM movies WHERE status = 'upcoming'";
$upcomingResult = mysqli_query($conn, $upcomingQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineWorld | Home</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
    <link rel="stylesheet" href="css/home.css?v=2">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="logo">CineWorld 🎥</div>
    <ul class="nav-links">
        <li><a href="#">Home</a></li>
        <li><a href="user_view_movies.php">Movies</a></li>
        <li><a href="cinema.php">Cinema</a></li>
        <li><a href="booking.php">My Bookings</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="login.php" onclick="confirmLogout()">Logout</a></li>
    </ul>
</nav>

<!-- Welcome -->
<section class="welcome" >
  <h2><i>Welcome, <?php echo htmlspecialchars($username); ?>!</i> 🍿</h2>
 <p><br><i>- Enjoy the latest in cinema experiences, handpicked just for you!</i></p>
</section>


<!-- Featured Movie -->
<section class="featured-movie">
    <div class="featured-heading">
        <div class="stripe-box">
            <div class="stripe stripe-wide"></div>
            <div class="stripe stripe-thin"></div>
        </div>
        <h3><i>Featured Now</i></h3>
    </div>
    <div class="featured-container">
    
<!-- featured cards -->
         <button class="arrow-button arrow-right" onclick="nextFeatured()">&#9654;</button>
         <button class="arrow-button arrow-left" onclick="prevFeatured()">&#9664;</button>
        <?php mysqli_data_seek($nowShowingResult, 0); ?>
        <?php while ($movie = mysqli_fetch_assoc($nowShowingResult)) { ?>
            <div class="featured-card">
                <img src="<?php echo $movie['poster_url']; ?>" alt="<?php echo $movie['title']; ?>">
                <div class="featured-info">
                    <h4><?php echo $movie['title']; ?></h4>
                    <div class="featured-buttons">
                        <a href="buy_ticket.php?movie=<?php echo $movie['id']; ?>" class="btn buy-ticket">Buy Ticket</a>
                        <a href="<?php echo $movie['trailer_url']; ?>" target="_blank" class="btn trailer-btn">Watch Trailer</a>
                    </div>
                </div>
            </div>
        <?php } ?>
        <button class="arrow-button arrow-right" onclick="nextFeatured()">&#9654;</button>
    </div>
</section>


<!-- Now Showing -->
<section class="now-showing">
    <h3>Now Showing</h3>
    <div class="movie-grid-container">
        <div class="movie-grid">
            <?php mysqli_data_seek($nowShowingResult, 0); ?>
            <?php while ($movie = mysqli_fetch_assoc($nowShowingResult)) { ?>
                <div class="movie-card">
                    <img src="<?php echo $movie['poster_url']; ?>" alt="<?php echo $movie['title']; ?>">
                    <div class="movie-details">
                        <h4><?php echo $movie['title']; ?></h4>
                        <p><?php echo $movie['screen_type']; ?></p>
                        <div class="movie-buttons">
                            <a href="buy_ticket.php?movie=<?php echo $movie['id']; ?>" class="btn buy-ticket">Buy Ticket</a>
                            <a href="<?php echo $movie['trailer_url']; ?>" target="_blank" class="btn trailer-btn">Watch Trailer</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Upcoming Movies -->
<section class="now-showing"> <!-- Reuse the same styling class -->
    <h3>Upcoming Movies</h3>
    <div class="movie-grid-container">
        <div class="movie-grid">
            <?php mysqli_data_seek($upcomingResult, 0); ?>
            <?php while ($movie = mysqli_fetch_assoc($upcomingResult)) { ?>
                <div class="movie-card">
                    <img src="<?php echo $movie['poster_url']; ?>" alt="<?php echo $movie['title']; ?>">
                    <div class="movie-details">
                        <h4><?php echo $movie['title']; ?></h4>
                        <p><?php echo $movie['screen_type']; ?></p>
                        <div class="movie-buttons">
                            <a href="<?php echo $movie['trailer_url']; ?>" target="_blank" class="btn trailer-btn">Watch Trailer</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>


<!-- AI Chat Interface -->
<div class="ai-chat-container">
    <div class="ai-chat-header">
        <h3>🎬 Movie AI Assistant</h3>
        <button class="close-chat">&times;</button>
    </div>
    <div class="ai-chat-messages" id="chatMessages">
        <div class="message ai-message">
            Hi there! I'm your movie assistant. Ask me about films, showtimes, or recommendations!
        </div>
    </div>
    <div class="ai-chat-input">
        <input type="text" id="userMessage" placeholder="Ask about movies...">
        <button id="sendMessage">Send</button>
    </div>
</div>
<button class="open-chat-btn">Ask AI About Movies</button>

<footer>
    <p>&copy; 2025 CineWorld. All rights reserved.</p>
</footer>

<script>
// Featured Movie Auto-Rotate
let currentFeatured = 0;
const featuredCards = document.querySelectorAll('.featured-card');
let interval;
let pauseTimeout;

function showFeatured(index) {
    featuredCards.forEach(card => card.classList.remove('active'));
    featuredCards[index].classList.add('active');
}

function nextFeatured() {
    currentFeatured = (currentFeatured + 1) % featuredCards.length;
    showFeatured(currentFeatured);
}

function prevFeatured() {
    currentFeatured = (currentFeatured - 1 + featuredCards.length) % featuredCards.length;
    showFeatured(currentFeatured);
}

function startAutoScroll() {
    interval = setInterval(nextFeatured, 3000);
}

function pauseAutoScroll() {
    clearInterval(interval);
    clearTimeout(pauseTimeout);
    pauseTimeout = setTimeout(() => {
        startAutoScroll();
    }, 3000);
}

// Initial setup
if (featuredCards.length > 1) {
    showFeatured(currentFeatured);
    startAutoScroll();
}

// Button interactions
document.getElementById('prevBtn').addEventListener('click', () => {
    prevFeatured();
    pauseAutoScroll();
});
document.getElementById('nextBtn').addEventListener('click', () => {
    nextFeatured();
    pauseAutoScroll();
});

// Movie Card Hover Effects
document.querySelectorAll('.movie-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.querySelector('.movie-details').style.opacity = '1';
    });
    card.addEventListener('mouseleave', () => {
        card.querySelector('.movie-details').style.opacity = '0';
    });
});

// AI Chat Functions
document.querySelector('.open-chat-btn').addEventListener('click', () => {
    document.querySelector('.ai-chat-container').style.display = 'flex';
});

document.querySelector('.close-chat').addEventListener('click', () => {
    document.querySelector('.ai-chat-container').style.display = 'none';
});

document.getElementById('sendMessage').addEventListener('click', sendMessage);
document.getElementById('userMessage').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage();
});

async function sendMessage() {
    const userInput = document.getElementById('userMessage').value.trim();
    if (!userInput) return;

    addMessage(userInput, 'user');
    document.getElementById('userMessage').value = '';
    
    const typingIndicator = addTypingIndicator();
    
    try {
        const response = await fetch('api/llama3_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: userInput, movies: getMovieData() })
        });
        const data = await response.json();
        
        typingIndicator.remove();
        addMessage(data.reply, 'ai');
    } catch (error) {
        typingIndicator.remove();
        addMessage("Sorry, I'm having trouble connecting. Please try again later.", 'ai');
    }
}

function addMessage(text, sender) {
    const chat = document.getElementById('chatMessages');
    const msg = document.createElement('div');
    msg.className = `message ${sender}-message`;

    msg.textContent = text;
    chat.appendChild(msg);
    chat.scrollTop = chat.scrollHeight;
}

function addTypingIndicator() {
    const typing = document.createElement('div');
    typing.className = 'message ai-message';
    typing.innerHTML = '<div class="typing-dots"><span>.</span><span>.</span><span>.</span></div>';
    document.getElementById('chatMessages').appendChild(typing);
    return typing;
}

function getMovieData() {
    // This would collect movie data from the page
    return Array.from(document.querySelectorAll('.movie-card')).map(card => ({
        title: card.querySelector('h4').textContent,
        screen_type: card.querySelector('p').textContent
    }));
}

function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}
</script>
</body>
</html>