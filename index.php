<?php
session_start();
require 'config.php'; // Include the database connection

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Handle login process if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $username);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "No user found with that email.";
    }

    $stmt->close();
}

// Fetch posts from the database if logged in
$posts = [];
if ($isLoggedIn) {
    $result = $conn->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC");
    if ($result->num_rows > 0) {
        while ($post = $result->fetch_assoc()) {
            // Fetch the like count for each post
            $post['like_count'] = get_like_count($post['id'], $conn);
            $posts[] = $post;
        }
    }
}

// Function to get the like count
function get_like_count($post_id, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count;
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
        /* Your styles here */
    </style>
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <h2>Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="author"><?php echo htmlspecialchars($post['username']); ?></div>
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <div class="like-counter"><?php echo htmlspecialchars($post['like_count']); ?> Likes</div>
            </div>
        <?php endforeach; ?>
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
    <?php endif; ?>
</body>
</html>


