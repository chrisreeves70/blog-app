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

// Get user ID from POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    // Prepare and execute the deletion query
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user: " . $conn->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();

// Redirect back to admin page
header('Location: admin.php');
exit;
?>
