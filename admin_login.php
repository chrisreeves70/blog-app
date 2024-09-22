<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form method="POST">
        <input type="text" name="admin_email" placeholder="Admin Username" required>
        <input type="password" name="admin_password" placeholder="Admin Password" required>
        <button type="submit">Admin Login</button>
    </form>

    <!-- Button to return to the homepage -->
    <button onclick="window.location.href='index.php'">Back to Home</button>
</body>
</html>
