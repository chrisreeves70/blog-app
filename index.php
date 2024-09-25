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

    // Prepare and execute SQL statement to find the user by email
    $stmt = $conn->prepare("SELECT id, password, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists in the database
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $username);
        $stmt->fetch();

        // Verify the provided password against the hashed password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id; // Store the user's ID in the session
            $_SESSION['username'] = $username; // Store the username in the session
            header("Location: index.php"); // Redirect to the main page
            exit();
        } else {
            $login_error = "Invalid password."; // Message for incorrect password
        }
    } else {
        $login_error = "No user found with that email."; // Message for non-existent user
    }

    $stmt->close(); // Close the prepared statement
}

// Fetch posts from the database if the user is logged in
$posts = [];
if ($isLoggedIn) {
    // Fetch posts along with the like counts from the database
    $result = $conn->query("SELECT posts.*, users.username, 
                                    (SELECT COUNT(*) FROM post_likes WHERE post_id = posts.id) AS like_count 
                             FROM posts 
                             JOIN users ON posts.user_id = users.id 
                             ORDER BY created_at DESC");
    if ($result->num_rows > 0) {
        $posts = $result->fetch_all(MYSQLI_ASSOC);
        
        // Fetch comments for each post
        foreach ($posts as &$post) {
            $post_id = $post['id'];
            $comment_result = $conn->query("SELECT comments.*, users.username AS commenter_username 
                                             FROM comments 
                                             JOIN users ON comments.user_id = users.id 
                                             WHERE comments.post_id = $post_id 
                                             ORDER BY comments.created_at ASC");
            $post['comments'] = $comment_result->fetch_all(MYSQLI_ASSOC); // Store comments in the post
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
            margin-bottom: 12px; /* Increased space below the author box */
            position: absolute; /* Position at the top left */
            top: 10px; /* Distance from top */
            left: 10px; /* Distance from left */
        }

        .post p {
            padding: 9px 0; /* Padding on top and bottom for spacing */
            margin: 45px 0 20px; /* Increased margin to create space from author box and bottom */
        }

        .comment {
            margin-top: 10px; /* Space above comments */
            padding: 5px; /* Padding for better appearance */
            border: 1px solid #ccc; /* Border for comments */
            border-radius: 5px; /* Round corners */
            background-color: #f9f9f9; /* Light background for comments */
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
                    <input type="text" class="comment-input" placeholder="Add a comment...">
                    <button class="comment-button">Comment</button>
                </div>

                <!-- Display comments related to this post -->
                <div class="comments">
                    <?php foreach ($post['comments'] as $comment): ?>
                        <div class="comment">
                            <strong><?php echo htmlspecialchars($comment['commenter_username']); ?>:</strong>
                            <?php echo htmlspecialchars($comment['content']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Logout button for the user -->
        <form action="logout.php" method="POST">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    <?php else: ?>
        <!-- Login form for users -->
        <h2>Login</h2>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <?php if (isset($login_error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($login_error); ?></p> <!-- Display login error messages -->
        <?php endif; ?>
        
        <!-- Links for admin login and sign up -->
        <a href="admin_login.php"><button class="admin-login-button">Admin Login</button></a>
        <a href="sign_up.php"><button class="sign-up-button">Sign Up</button></a>
    <?php endif; ?>
</body>
</html>

