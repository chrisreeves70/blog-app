<?php
session_start();
require 'config.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

// Handle post creation if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

    // Prepare and execute SQL statement to insert a new post
    $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php"); // Redirect to the main page after creating the post
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Create Post</title>
    <style>
        body {
            text-align: center; /* Center all text */
        }
        form {
            margin: 20px auto; /* Center forms */
            width: 25%; /* Set form width */
        }
        input[type="text"],
        textarea,
        button {
            width: 100%; /* Full width for inputs and buttons */
            margin: 10px 0; /* Margin for spacing */
            padding: 10px; /* Padding for better touch targets */
            border: 1px solid black; /* Black border */
            border-radius: 5px; /* Round corners */
        }
        button {
            color: black; /* Black text color */
            cursor: pointer; /* Pointer cursor on hover */
        }
    </style>
</head>
<body>
    <h2>Create Post</h2>
    <form action="" method="post">
        <!-- Title input -->
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <!-- Content input -->
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>

        <!-- Submit button -->
        <button type="submit">Create Post</button>
    </form>

    <!-- Back to home button -->
    <button onclick="window.location.href='index.php'" style="width: 25%;">Back to Home</button>
</body>
</html>

