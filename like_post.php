session_start();
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'] ?? null;
$user_id = $_SESSION['user_id']; // Get the logged-in user ID

if ($post_id) {
    // Check if the user has already liked the post
    $stmt = $conn->prepare("SELECT * FROM post_likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Already liked.']);
    } else {
        // Increment like count
        $stmt = $conn->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();

        // Record the like in the new table
        $stmt = $conn->prepare("INSERT INTO post_likes (user_id, post_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();

        echo json_encode(['success' => true, 'new_like_count' => $likes + 1]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid post ID.']);
}

