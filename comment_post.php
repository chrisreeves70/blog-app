<?php
session_start();
require 'config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null; // Get the logged-in user ID
    $comment = $_POST['comment'] ?? '';

    if ($post_id && $user_id && !empty($comment)) {
        // Prepare and execute SQL statement to insert comment
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $comment);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    }
}

$conn->close(); // Close the database connection
?>
