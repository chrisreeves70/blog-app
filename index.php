<?php
session_start();
require 'config.php'; // Include the database connection

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Fetch posts from the database
$posts = [];
if ($isLoggedIn) {
    $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
    if ($result->num_rows > 0) {
        $posts = $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $conn->query("INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')");
    echo "Registration successful! Please log in.";
}

// Handle user login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }
}

// Handle admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $adminUsername = $_POST['admin_email'];
    $adminPassword = $_POST['admin_password'];

    // Hardcoded admin credentials
    if ($adminUsername === 'admin' && $adminPassword === '1234') {
        $_SESSION['user_id'] = 0;  // Admin doesn't need a user ID
        $_SESSION['role'] = 'admin';  // Set admin role
        header("Location: admin.php");  // Redirect to admin dashboard
        exit();
    } else {
        echo "Invalid admin credentials!";
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
    <!-- Display posts if logged in -->
    <?php if ($isLoggedIn): ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <h2>Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div>
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <h1>Login to view posts</h1>
    <?php endif; ?>

    <!-- User Registration Form -->
    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Register</button>
    </form>

    <!-- User Login Form -->
    <h2>User Login</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <!-- Admin Login Form -->
    <h2>Admin Login</h2>
    <form method="POST">
        <input type="text" name="admin_email" placeholder="Admin Username" required>
        <input type="password" name="admin_password" placeholder="Admin Password" required>
        <button type="submit" name="admin_login">Admin Login</button>
    </form>
</body>
</html>