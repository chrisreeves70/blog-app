session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $content = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    // Insert comment
    $stmt = $conn->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $post_id, $content);
    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect back to index
        exit();
    } else {
        echo "Error commenting on post: " . $conn->error;
    }
}
