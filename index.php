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
        /* Add your styles here */
        /* Same styles as before */
    </style>
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <button class="create-post-button" onclick="window.location.href='create_post.php'">Create Post</button>
        <h2>Posts</h2>

        <?php
        // Fetch likes count and comments for each post
        foreach ($posts as $post) {
            // Fetch likes count
            $likeCountStmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
            $likeCountStmt->bind_param("i", $post['id']);
            $likeCountStmt->execute();
            $likeCountStmt->bind_result($likeCount);
            $likeCountStmt->fetch();
            $likeCountStmt->close();

            // Fetch comments for each post
            $commentStmt = $conn->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ?");
            $commentStmt->bind_param("i", $post['id']);
            $commentStmt->execute();
            $commentsResult = $commentStmt->get_result();

            echo "<div class='post'>";
            echo "<div class='author'>" . htmlspecialchars($post['username']) . "</div>";
            echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
            echo "<p>" . htmlspecialchars($post['content']) . "</p>";
            echo "<p>Likes: $likeCount</p>";
            echo "<form method='POST' action='like_post.php'>
                    <input type='hidden' name='post_id' value='" . $post['id'] . "'>
                    <button type='submit'>Like</button>
                  </form>";

            echo "<h4>Comments:</h4>";
            while ($comment = $commentsResult->fetch_assoc()) {
                echo "<p><strong>" . htmlspecialchars($comment['username']) . ":</strong> " . htmlspecialchars($comment['content']) . "</p>";
            }
            echo "<form method='POST' action='comment_post.php'>
                    <input type='hidden' name='post_id' value='" . $post['id'] . "'>
                    <input type='text' name='comment' placeholder='Add a comment...' required>
                    <button type='submit'>Comment</button>
                  </form>";
            echo "</div>";
        }
        ?>

        <!-- Logout button -->
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

        <!-- Sign up and Admin login buttons -->
        <button class="sign-up-button" onclick="window.location.href='register.php'">Not a User? Sign Up</button>
        <button class="admin-login-button" onclick="window.location.href='admin_login.php'">Admin Login</button>
    <?php endif; ?>
</body>
</html>

