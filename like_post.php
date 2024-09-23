session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Insert like
    $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $post_id);
    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect back to index
        exit();
    } else {
        echo "Error liking post: " . $conn->error;
    }
}
