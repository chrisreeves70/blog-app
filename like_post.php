<?php
session_start();
require 'config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // Prepare and execute SQL statement to increment likes
    $stmt = $conn->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    
    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect back to index
        exit();
    } else {
        // Handle error
        error_log("Failed to update likes for post ID: $post_id");
        header("Location: index.php?error=update_failed");
        exit();
    }

    $stmt->close(); // Close statement
} else {
    header("Location: index.php"); // Redirect if not a POST request
    exit();
}
