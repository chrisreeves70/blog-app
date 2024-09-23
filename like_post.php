<?php
session_start();
require 'config.php'; // Include database connection

// Check if the request is a POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
    $data = json_decode(file_get_contents('php://input'), true);
    $postId = $data['post_id'];

    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        // Insert like into the database
        $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $postId);

        if ($stmt->execute()) {
            // Count the total likes for this post
            $likeCountStmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
            $likeCountStmt->bind_param("i", $postId);
            $likeCountStmt->execute();
            $likeCountStmt->bind_result($likeCount);
            $likeCountStmt->fetch();

            // Return success response
            echo json_encode(['success' => true, 'new_like_count' => $likeCount]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Could not like post.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
