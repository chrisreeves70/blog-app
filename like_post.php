<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

// Get the post ID from the request
$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'] ?? null;

if ($post_id) {
    // Check if the post exists
    $stmt = $conn->prepare("SELECT likes FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->bind_result($likes);
    $stmt->fetch();
    
    if ($likes !== null) {
        // Increment like count
        $new_likes = $likes + 1;
        $stmt->close();
        
        // Update the likes in the database
        $stmt = $conn->prepare("UPDATE posts SET likes = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_likes, $post_id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'new_like_count' => $new_likes]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Post not found.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid post ID.']);
}
?>
