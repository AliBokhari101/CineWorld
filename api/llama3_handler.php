<?php
require_once '../config/db.php';

header('Content-Type: application/json');

// Rate limiting (2 requests per second)
session_start();
if (!isset($_SESSION['last_request'])) {
    $_SESSION['last_request'] = 0;
}

if (time() - $_SESSION['last_request'] < 0.5) {
    die(json_encode(['reply' => "Please wait a moment before sending another message."]));
}
$_SESSION['last_request'] = time();

// Get input
$data = json_decode(file_get_contents('php://input'), true);
$message = trim($data['message'] ?? '');
$movies = $data['movies'] ?? [];

if (empty($message)) {
    die(json_encode(['reply' => "Please type a message."]));
}

// Build context from database
$movieContext = "";
$result = $conn->query("SELECT title, genre, description, show_time FROM movies WHERE status = 'now_showing'");
while ($row = $result->fetch_assoc()) {
    $movieContext .= "Title: {$row['title']}, Genre: {$row['genre']}, Description: {$row['description']}\n";
}

// System prompt
$systemPrompt = <<<PROMPT
You are CineWorld's AI assistant. Help users with:
- Movie recommendations
- Showtimes information
- Genre suggestions
- General cinema questions

Current movies showing:
$movieContext

Answer concisely (1-2 sentences max) and be friendly.
PROMPT;

// For production: Use actual Llama3 API
// This is a mock implementation for demonstration
function mockLlama3($prompt) {
    $responses = [
        "I recommend checking out our featured action movies playing this week!",
        "Our next showing is at 7:30 PM tonight.",
        "We have great family-friendly options available.",
        "That's one of our most popular films - tickets are selling fast!",
        "You might enjoy our new sci-fi release based on your preferences."
    ];
    return $responses[array_rand($responses)];
}

$response = mockLlama3($systemPrompt . "\n\nUser: " . $message);

// In production, replace with:
// $response = queryLlama3API($systemPrompt, $message);

echo json_encode(['reply' => $response]);