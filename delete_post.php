
<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: index.php');
    exit;
}

// Database connection
$servername = 'your_database_host'; // Replace with your JawsDB host
$username = 'your_database_username'; // Replace with your JawsDB username
$password = 'your_database_password'; // Replace with your JawsDB password
$dbname = 'your_database_name'; // Replace with your JawsDB database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get post ID from POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    
    // Prepare and execute the deletion query
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    
    if ($stmt->execute()) {
        echo "Post deleted successfully.";
    } else {
        echo "Error deleting post: " . $conn->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();

// Redirect back to admin page
header('Location: admin.php');
exit;
?>
