<?php
session_start();
require 'config.php'; // Include the database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

// Handle the form submission for creating a post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Prepare and execute SQL statement to insert the new post
    $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $user_id);

    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect to homepage after successful post
        exit();
    } else {
        $error = "Error creating post. Please try again."; // Display error message
    }

    $stmt->close(); // Close statement
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <style>
        body {
            text-align: center; /* Center all text */
            font-family: Arial, sans-serif; /* Set font */
        }
        form {
            margin: 20px auto; /* Center form */
            width: 25%; /* Set form width */
        }
        input[type="text"],
        textarea {
            width: 100%; /* Full width for inputs */
            margin: 10px 0; /* Add margin between inputs */
            padding: 10px; /* Padding for better touch targets */
            border: 1px solid black; /* Black border */
            border-radius: 5px; /* Round corners */
        }
        textarea {
            height: 150px; /* Set height for the content area */
        }
        button {
            width: auto; /* Auto-size buttons */
            padding: 10px 20px; /* Add padding */
            margin: 10px 5px; /* Add margin between buttons */
            border-radius: 5px; /* Round corners */
            border: 1px solid black; /* Black border */
            cursor: pointer; /* Pointer cursor on hover */
        }
        .create-post-button {
            background-color: #90EE90; /* Green background for create post button */
            color: black; /* Black text */
        }
        .back-home-button {
            background-color: #007BFF ; /*background for back to home button */
            color: black; /* Black text */
        }
    </style>
</head>
<body>
    <h1>Create a New Post</h1>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="title" placeholder="Post Title" required>
        <textarea name="content" placeholder="Write your post here..." required></textarea>
        <button type="submit" class="create-post-button">Create Post</button>
    </form>
    
    <!-- Back to home button -->
    <button class="back-home-button" onclick="window.location.href='index.php'">Back to Home</button>
</body>
</html>


