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
    // Fetch posts along with like counts
    $result = $conn->query("SELECT posts.*, users.username, 
                                    (SELECT COUNT(*) FROM post_likes WHERE post_id = posts.id) AS like_count 
                             FROM posts 
                             JOIN users ON posts.user_id = users.id 
                             ORDER BY created_at DESC");
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
            text-align: center; /* Center all text */
        }
        .post {
            margin: 20px auto; /* Center and add margin between posts */
            width: 22.5%; /* Set post width */
            border: 1px solid #ccc; /* Optional: add a border */
            padding: 9px; /* Optional: add padding */
            border-radius: 5px; /* Optional: round corners */
            position: relative; /* Positioning for absolute elements */
            word-wrap: break-word; /* Ensure long titles wrap to the next line */
        }
        .author {
            background-color: #007BFF; /* Blue background for author rectangle */
            color: black; /* Black text color */
            padding: 4.5px; /* Padding for better appearance */
            border-radius: 5px; /* Round corners */
            border: 1px solid black; /* Black border around the rectangle */
            display: inline-block; /* Inline block for rectangle */
            margin-bottom: 9px; /* Space below the author box */
            position: absolute; /* Position at the top left */
            top: 10px; /* Distance from top */
            left: 10px; /* Distance from left */
        }
        .post p {
            padding-bottom: 9px; /* Reduced padding at the bottom */
            margin-bottom: 45px; /* Added margin to create space below the content */
        }
        form {
            margin: 20px auto; /* Center forms */
            width: 22.5%; /* Set form width */
        }
        input[type="email"],
        input[type="password"],
        button {
            width: 100%; /* Full width for inputs and buttons */
            margin: 9px 0; /* Margin for spacing */
            padding: 9px; /* Padding for better touch targets */
            border: 1px solid black; /* Black border */
            border-radius: 5px; /* Round corners */
        }
        button {
            color: black; /* Black text color */
            cursor: pointer; /* Pointer cursor on hover */
        }
        button[type="submit"] {
            background-color: #90EE90; /* Light green for login button */
        }
        .create-post-button {
            background-color: #90EE90; /* Green for create post button */
            width: auto; /* Make button auto-sized */
            padding: 9px 18px; /* Adjust padding */
        }
        .logout-button {
            background-color: #FF4500 !important; /* Red for logout button with !important to override other styles */
            color: black !important; /* Black color with !important */
            width: auto; /* Make button auto-sized */
            padding: 9px 18px; /* Adjust padding */
            margin: 9px auto; /* Center the button */
            border: 1px solid black; /* Add black border */
            border-radius: 5px; /* Round corners */
        }
        .sign-up-button {
            background-color: #007BFF; /* Muted blue for sign-up button */
            width: auto; /* Make button auto-sized */
            padding: 9px 18px; /* Adjust padding */
        }
        .admin-login-button {
            background-color: #FF4500; /* Muted red for admin login button */
            width: auto; /* Make button auto-sized */
            padding: 9px 18px; /* Adjust padding */
        }
        .like-button, .comment-button {
            background-color: #90EE90; /* Green for buttons */
            color: black; /* Black text */
            border: 1px solid black; /* Black border */
            border-radius: 5px; /* Round corners */
            cursor: pointer; /* Pointer cursor on hover */
            padding: 4.5px 9px; /* Padding for buttons */
            margin: 4.5px; /* Space between buttons */
        }
        .like-counter {
            background-color: #fadce0; /* Red background for the like counter */
            color: black; /* Black text color */
            border: 1px solid black; /* Black border */
            border-radius: 30px; /* Round bubble shape */
            padding: 4.5px 9px; /* Padding for the bubble */
            display: inline-block; /* Inline block for the counter */
            margin: 4.5px; /* Space between counter and buttons */
            position: absolute; /* Positioning for absolute placement */
            top: 10px; /* Adjusted distance from the top */
            right: 10px; /* Distance from the right */
            padding: 5px 10px; /* Additional padding */
        }
        .button-container {
            margin-top: 27px; /* Space above buttons */
            display: flex; /* Use flexbox for alignment */
            justify-content: space-between; /* Space out buttons evenly */
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
                <!-- Author username displayed in a rectangle -->
                <div class="author"><?php echo htmlspecialchars($post['username']); ?></div>
                <p style="text-align: center;"><?php echo htmlspecialchars($post['content']); ?></p>

                <!-- Like counter positioned at the top right -->
                <div class="like-counter"><?php echo htmlspecialchars($post['like_count']); ?> Likes</div>

                <div class="button-container">
                    <!-- Like button with post ID -->
                    <button class="like-button" data-post-id="<?php echo $post['id']; ?>">Like</button>
                    
                    <!-- Comment input field -->
                    <input type="text" class="comment-input" placeholder="Add a comment..." style="flex-grow: 1; margin: 0 4.5px;">
                    <button class="comment-button">>>></button>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Logout button -->
        <form method="POST" action="logout.php">
            <button type="submit" class="logout-button">Logout</button>
        </form>

    <?php else: ?>
        <!-- Login form -->
        <h2>Login</h2>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <?php if (isset($login_error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($login_error); ?></p>
        <?php endif; ?>

        <button class="sign-up-button" onclick="window.location.href='signup.php'">Sign Up</button>
        <button class="admin-login-button" onclick="window.location.href='admin_login.php'">Admin Login</button>
    <?php endif; ?>
</body>
</html>

