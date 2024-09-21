<?php
session_start(); // Start the session
include 'config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        // Retrieve form data
        $title = $_POST['title'];
        $content = $_POST['content'];
        $user_id = $_SESSION['user_id'];

        // Prepare and execute SQL statement to insert post
        $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $content, $user_id);

        // Check if post creation was successful
        if ($stmt->execute()) {
            echo "Post created successfully!";
        } else {
            echo "Error: " . $stmt->error; // Output any error
        }

        $stmt->close(); // Close statement
    } else {
        echo "You must be logged in to create a post."; // Not logged in message
    }
    $conn->close(); // Close database connection
}
?>
