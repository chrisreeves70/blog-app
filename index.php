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
            width: 25%; /* Set post width */
            border: 1px solid #ccc; /* Optional: add a border */
            padding: 10px; /* Optional: add padding */
            border-radius: 5px; /* Optional: round corners */
            position: relative; /* Positioning for absolute elements */
            word-wrap: break-word; /* Ensure long titles wrap to the next line */
        }
        .author {
            background-color: #007BFF; /* Blue background for author rectangle */
            color: black; /* Black text color */
            padding: 5px; /* Padding for better appearance */
            border-radius: 5px; /* Round corners */
            border: 1px solid black; /* Black border around the rectangle */
            display: inline-block; /* Inline block for rectangle */
            margin-bottom: 10px; /* Space below the author box */
            position: absolute; /* Position at the top left */
            top: 10px; /* Distance from top */
            left: 10px; /* Distance from left */
        }
        .post h3 {
            margin-left: 80px; /* Create space from the left so it doesn't overlap with the author box */
            word-wrap: break-word; /* Break long words into the next line */
            margin-top: 10px; /* Add top margin to give more space from the top */
        }
        .post p {
            padding-bottom: 30px; /* Add padding at the bottom to prevent overlap with like button */
        }
        form {
            margin: 20px auto; /* Center forms */
            width: 25%; /* Set form width */
        }
        input[type="email"],
        input[type="password"],
        button {
            width: 100%; /* Full width for inputs and buttons */
            margin: 10px 0; /* Margin for spacing */
            padding: 10px; /* Padding for better touch targets */
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
            padding: 10px 20px; /* Adjust padding */
        }
        .logout-button {
            background-color: #FF4500 !important; /* Red for logout button */
            color: black !important; /* Black color with !important */
            width: auto; /* Make button auto-sized */
            padding: 10px 20px; /* Adjust padding */
            margin: 10px auto; /* Center the button */
            border: 1px solid black; /* Add black border */
            border-radius: 5px; /* Round corners */
        }
        .sign-up-button {
            background-color: #007BFF; /* Muted blue for sign-up button */
            width: auto; /* Make button auto-sized */
            padding: 10px 20px; /* Adjust padding */
        }
        .admin-login-button {
            background-color: #FF4500; /* Muted red for admin login button */
            width: auto; /* Make button auto-sized */
            padding: 10px 20px; /* Adjust padding */
        }
        .like-button {
            background-color: #90EE90; /* green for Like button */
            color: black; /* Black text */
            border: 1px solid black; /* Black border */
            padding: 2px 4px; /* Reduced padding for size */
            border-radius: 5px; /* Round corners */
            cursor: pointer; /* Pointer cursor on hover */
            font-size: 9px; /* Smaller font size */
            margin-top: 10px; /* Space above button */
            width: 60px; /* Fixed width for the button */
            position: absolute; /* Position relative to the post */
            bottom: 10px; /* Distance from the bottom of the post */
            left: 10px; /* Distance from the left side */
        }
        .like-counter {
            background-color: #fadce0; /* Red background for the like counter */
            color: black; /* Black text color */
            border: 1px solid black; /* Black border */
            border-radius: 30px; /* Round bubble shape */
            padding: 5px 10px; /* Padding for the bubble */
            position: absolute; /* Position relative to the post */
            bottom: 10px; /* Distance from the bottom */
            right: 10px; /* Distance from the right side */
            font-size: 10px; /* Font size for the counter */
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

                <form method="POST" action="like_post.php" style="display: inline;">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" class="like-button">Like</button>
                </form>
                
                <div class="like-counter"><?php echo htmlspecialchars($post['likes']); ?> Likes</div>
            </div>
        <?php endforeach; ?>

        <form method="POST" action="logout.php">
            <button type="submit" class="logout-button">Logout</button>
        </form>

        <!-- Admin login button -->
        <button class="admin-login-button" onclick="window.location.href='admin_login.php'">Admin Login</button>

    <?php else: ?>
        <h1>Login to view posts</h1>
        <?php if (isset($login_error)): ?>
            <p style="color:red;"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" required placeholder="Email">
            <input type="password" name="password" required placeholder="Password">
            <button type="submit" name="login">Login</button>
        </form>
        <button class="sign-up-button" onclick="window.location.href='signup.php'">Sign Up</button>
    <?php endif; ?>
</body>
</html>

