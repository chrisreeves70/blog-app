<?php
session_start();
include 'config.php'; // Include database connection

// Fetch posts from the database
$result = $conn->query("SELECT posts.id, posts.title, posts.content, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<h2>" . $row['title'] . "</h2>";
        echo "<p>By " . $row['username'] . "</p>";
        echo "<p>" . $row['content'] . "</p>";
        echo "<hr>";
    }
} else {
    echo "No posts available.";
}

$conn->close(); // Close the database connection
?>
