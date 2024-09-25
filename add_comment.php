<?php
session_start();
require 'config.php'; // Include the database connection

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true); // Get JSON input
    $post_id = $input['post_id'] ?? null; // Get post ID
    $comment = $input['comment'] ?? null; // Get comment

    if ($post_id && $comment && isset($_SESSION['user_id'])) {
        // Prepare and execute SQL statement to insert the comment
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $_SESSION['user_id'], $comment);

        if ($stmt->execute()) {
            // Get the username of the commenter
            $commenter_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
            $commenter_stmt->bind_param("i", $_SESSION['user_id']);
            $commenter_stmt->execute();
            $commenter_stmt->bind_result($commenter_username);
            $commenter_stmt->fetch();
            $commenter_stmt->close();

            // Return success response
            echo json_encode([
                'success' => true,
                'commenter_username' => htmlspecialchars($commenter_username), // Ensure safe output
                'comment' => htmlspecialchars($comment), // Ensure safe output
            ]);
        } else {
            // Return error response
            echo json_encode(['success' => false, 'error' => 'Failed to add comment.']);
        }
        $stmt->close(); // Close statement
    } else {
        // Return error response
        echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    }
} else {
    // Return error response
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
