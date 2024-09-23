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

// Handle like functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    $postId = $_POST['post_id'];

    // Insert or update like count in the database
    $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE post_id = post_id");
    $stmt->bind_param("ii", $postId, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Handle comment functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $postId = $_POST['post_id'];
    $comment = $_POST['comment_text'];

    // Insert comment into the database
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postId, $_SESSION['user_id'], $comment);
    $stmt->execute();
    $stmt->close();
}

// Fetch posts from the database if logged in
$posts = [];
if ($isLoggedIn) {
    $result = $conn->query("SELECT posts.*, users.username, 
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY created_at DESC");
    if ($result->num_rows > 0) {
        $posts = $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Fetch comments for each post
$comments = [];
if ($isLoggedIn) {
    $commentResult = $conn->query("SELECT comments.*, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id");
    if ($commentResult->num_rows > 0) {
        while ($row = $commentResult->fetch_assoc()) {
            $comments[$row['post_id']][] = $row;
        }
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
            text-align: center;
        }
        .post {
            margin: 20px auto;
            width: 25%;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            position: relative;
            word-wrap: break-word;
        }
        .author {
            background-color: #007BFF;
            color: black;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid black;
            display: inline-block;
            margin-bottom: 10px;
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .post h3 {
            margin-left: 80px;
            word-wrap: break-word;
            margin-top: 10px;
        }
        form {
            margin: 20px auto;
            width: 25%;
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
        button[type="submit"] {
            background-color: #90EE90;
        }
        .create-post-button {
            background-color: #90EE90;
            width: auto;
            padding: 10px 20px;
        }
        .logout-button {
            background-color: #FF4500 !important;
            color: black !important;
            width: auto;
            padding: 10px 20px;
            margin: 10px auto;
            border: 1px solid black;
            border-radius: 5px;
        }
        .sign-up-button {
            background-color: #007BFF;
            width: auto;
            padding: 10px 20px;
        }
        .admin-login-button {
            background-color: #FF4500;
            width: auto;
            padding: 10px 20px;
        }
        .like-button {
            background-color: #FFD700;
            padding: 5px 10px;
            margin: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid black;
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
                <form method="POST">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" name="like" class="like-button">
                        Like (<?php echo $post['like_count']; ?>)
                    </button>
                </form>
                <h4>Comments</h4>
                <?php if (isset($comments[$post['id']])): ?>
                    <?php foreach ($comments[$post['id']] as $comment): ?>
                        <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
                <form method="POST" class="comment-form">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <textarea name="comment_text" placeholder="Write a comment..." required></textarea>
                    <button type="submit" name="comment">Comment</button>
                </form>
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
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <button class="sign-up-button" onclick="window.location.href='register.php'">Not a User? Sign Up</button>
        <button class="admin-login-button" onclick="window.location.href='admin_login.php'">Admin Login</button>
    <?php endif; ?>
</body>
</html>

