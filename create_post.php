<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // Get the ID of the logged-in user

    // Insert the new post into the database
    $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $user_id);

    if ($stmt->execute()) {
        echo "Post created successfully!";
        header("Location: index.php"); // Redirect to main page after creating post
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
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
        textarea {
            width: 100%; /* Full width for inputs */
            margin: 10px 0; /* Margin for spacing */
            padding: 10px; /* Padding for better touch targets */
            border: 1px solid black; /* Black border */
            border-radius: 5px; /* Round corners */
        }
        button {
            width: 100%; /* Full width for button */
            padding: 10px; /* Padding for better touch targets */
            border: 1px solid black; /* Black border */
            border-radius: 5px; /* Round corners */
            background-color: #007BFF; /* Blue for the button */
            color: black; /* Black text color */
            cursor: pointer; /* Pointer cursor on hover */
        }
    </style>
</head>
<body>
    <h2>Create Post</h2>
    <form method="POST">
        <!-- Title input -->
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <!-- Content input -->
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>

        <!-- Submit button -->
        <button type="submit">Create Post</button>
    </form>

    <button onclick="window.location.href='index.php'">Back to Home</button>
</body>
</html>
