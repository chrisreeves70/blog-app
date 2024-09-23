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
        }
    } else {
        $login_error = "No user found with that email."; // No user message
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

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $postId = $_POST['post_id'];
    $comment = $_POST['comment_text'];
    $userId = $_SESSION['user_id'];

    // Insert the comment into the database
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postId, $userId, $comment);
    $stmt->execute();
    $stmt->close();
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
            text-align: center;
        }
        .post {
            margin: 20px auto;
            width: 25%;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
        }
        .author {
            background-color: #007BFF;
            color: black;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid black;
            display: inline-block;
            margin-bottom: 10px;
        }
        .post h3 {
            margin-left: 80px;
            margin-top: 10px;
        }
        input[type="email"],
        input[type="password"],
        button {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid black;
            border-radius: 5px;
        }
        button {
            color: black;
            cursor: pointer;
        }
        .like-button {
            background-color: #FF4500;
            color: black;
            border: 1px solid black;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 10px;
        }
        .comment-button {
            background-color: #007BFF;
        }
        .comment-box {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <button onclick="window.location.href='create_post.php'">Create Post</button>
        <h2>Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post" data-post-id="<?php echo $post['id']; ?>">
                <div class="author"><?php echo htmlspecialchars($post['username']); ?></div>
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>

                <!-- Like button -->
                <button class="like-button">Like</button>

                <!-- Comment form -->
                <div class="comment-box">
                    <form method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <input type="text" name="comment_text" placeholder="Add a comment..." required>
                        <button type="submit" name="comment">Submit</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Logout button -->
        <form method="POST" action="logout.php">
            <button type="submit">Logout</button>
        </form>

    <?php else: ?>
        <h1>Login to view posts</h1>
        <?php if (isset($login_error)): ?>
            <p style="color:red;"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <button onclick="window.location.href='register.php'">Not a User? Sign Up</button>
        <button onclick="window.location.href='admin_login.php'">Admin Login</button>
    <?php endif; ?>
</body>
</html>


