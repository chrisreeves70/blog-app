<?php
session_start();
require 'config.php'; // Include the database connection

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch posts from the database if logged in
$posts = [];
if ($isLoggedIn) {
    $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
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
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <!-- If user is logged in, display posts -->
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <h2>Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div>
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
            </div>
        <?php endforeach; ?>

        <!-- Logout button -->
        <form method="POST" action="logout.php">
            <button type="submit">Logout</button>
        </form>

    <?php else: ?>
        <!-- If user is not logged in, show login form and buttons -->
        <h1>Login to view posts</h1>
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- Sign up and Admin login buttons -->
        <button onclick="window.location.href='register.php'">Not a User? Sign Up</button>
        <button onclick="window.location.href='admin_login.php'">Admin Login</button>
    <?php endif; ?>
</body>
</html>

