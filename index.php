<?php
session_start();
require 'config.php'; // Include the database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Handle login process if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute SQL statement to find user by email
    $stmt = $conn->prepare("SELECT id, password, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $username);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id; // Store user ID in session
            $_SESSION['username'] = $username; // Store username in session
            header("Location: index.php"); // Redirect to self
            exit();
        } else {
            $login_error = "Invalid password."; // Incorrect password message
            error_log("Invalid password for email: $email"); // Log for debugging
        }
    } else {
        $login_error = "No user found with that email."; // No user message
        error_log("No user found with email: $email"); // Log for debugging
    }

    $stmt->close(); // Close statement
}

// Fetch posts from the database if logged in
$posts = [];
if ($isLoggedIn) {
    $result = $conn->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC");
    if ($result->num_rows > 0) {
        $posts = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Multi-User Blog</title>
    <style>
        body {
            background-color: #f5f5f5; /* Light background */
            color: #333; /* Text color */
            font-family: Arial, sans-serif;
            text-align: center; /* Center all text */
        }
        .post {
            margin: 20px auto;
            width: 50%; /* Adjust width for better appearance */
            background-color: white;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add subtle shadow */
            position: relative;
        }
        .author {
            background-color: #007BFF;
            color: white;
            padding: 5px 10px;
            border-radius: 8px;
            position: absolute;
            top: 10px;
            left: 10px;
            font-weight: bold;
        }
        .post h3 {
            margin-left: 80px;
            text-align: left; /* Align title to left */
            color: #007BFF; /* Keep heading color consistent */
        }
        .post p {
            text-align: left;
        }
        form {
            margin: 20px auto;
            width: 300px;
        }
        input[type="email"],
        input[type="password"],
        button {
            width: 100%;
            margin: 10px 0;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .create-post-button {
            background-color: #90EE90;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 8px;
        }
        .logout-button {
            background-color: #FF4500;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 8px;
        }
        .sign-up-button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        .admin-login-button {
            background-color: #FF4500;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <button class="create-post-button" onclick="window.location.href='create_post.php'">Create Post</button>
        <h2>Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="author"><?php echo htmlspecialchars($post['username']); ?></div>
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
            </div>
        <?php endforeach; ?>

        <form method="POST" action="logout.php">
            <button type="submit" class="logout-button">Logout</button>
        </form>

    <?php else: ?>
        <h1>Login to view posts</h1>
        <?php if (isset($login_error)): ?>
            <p style="color:red;"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <div class="form-container">
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <button class="sign-up-button" onclick="window.location.href='register.php'">Not a User? Sign Up</button>
            <button class="admin-login-button" onclick="window.location.href='admin_login.php'">Admin Login</button>
        </div>
    <?php endif; ?>
</body>
</html>


