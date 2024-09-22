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
        echo "<p style='color:red;'>Invalid admin credentials!</p>";
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
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Light background color */
            margin: 0;
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px; /* Add padding inside the input */
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Ensure padding is included in total width */
        }
        button {
            background-color: #007BFF; /* Primary color */
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .back-button {
            margin-top: 10px;
            background-color: #90EE90; /* Light green */
        }
        .back-button:hover {
            background-color: #76c776; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="POST">
            <input type="text" name="admin_email" placeholder="Admin Username" required>
            <input type="password" name="admin_password" placeholder="Admin Password" required>
            <button type="submit">Admin Login</button>
        </form>

        <!-- Button to return to the homepage -->
        <button class="back-button" onclick="window.location.href='index.php'">Back to Home</button>
    </div>
</body>
</html>

