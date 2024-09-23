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
    $comment = $_POST['comment'];
    $postId = $_POST['post_id'];
    $userId = $_SESSION['user_id'];

    // Insert comment into database
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postId, $userId, $comment);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php"); // Redirect to self to avoid resubmission
    exit();
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
        button,
        textarea {
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
        .sign-up-button,
        .admin-login-button {
            width: auto;
            padding: 10px 20px;
        }
        .post-actions {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .like-button {
            background-color: red;
            color: black;
            border: 1px solid black;
            padding: 3px 5px;
            border-radius: 5px;
            margin-right: 5px;
            cursor: pointer;
            font-size: 10px;
        }
        .comment-box {
            margin-top: 15px; /* Spacing above comment box */
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
                <p style="text-align: center;"><?php echo htmlspecialchars($post['content']); ?></p>

                <!-- Like and Comment buttons -->
                <div class="post-actions">
                    <button class="like-button">Like</button>
                </div>

                <!-- Comment form -->
                <div class="comment-box">
                    <form method="POST">
                        <textarea name="comment" placeholder="Write your comment..." required></textarea>
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" name="comment">Submit Comment</button>
                    </form>
                </div>

                <!-- Display comments for this post -->
                <div class="comments">
                    <?php
                    $comment_stmt = $conn->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY created_at DESC");
                    $comment_stmt->bind_param("i", $post['id']);
                    $comment_stmt->execute();
                    $comment_result = $comment_stmt->get_result();

                    while ($comment = $comment_result->fetch_assoc()) {
                        echo '<div><strong>' . htmlspecialchars($comment['username']) . ':</strong> ' . htmlspecialchars($comment['content']) . '</div>';
                    }

                    $comment_stmt->close();
                    ?>
                </div>
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

