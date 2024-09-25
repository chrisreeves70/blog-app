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
        
        // Fetch comments for each post
        foreach ($posts as &$post) {
            $post_id = $post['id'];
            $comment_result = $conn->query("SELECT comments.*, users.username AS commenter_username 
                                             FROM comments 
                                             JOIN users ON comments.user_id = users.id 
                                             WHERE comments.post_id = $post_id 
                                             ORDER BY comments.created_at ASC");
            $post['comments'] = $comment_result->fetch_all(MYSQLI_ASSOC);
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

                <!-- Display comments for the post -->
                <div class="comments-section">
                    <?php if (!empty($post['comments'])): ?>
                        <?php foreach ($post['comments'] as $comment): ?>
                            <div class="comment">
                                <strong><?php echo htmlspecialchars($comment['commenter_username']); ?>:</strong>
                                <span><?php echo htmlspecialchars($comment['comment']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <form method="post">
            <button class="logout-button" type="submit" name="logout">Logout</button>
        </form>
    <?php else: ?>
        <h1>Login</h1>
        <?php if (isset($login_error)): ?>
            <p><?php echo htmlspecialchars($login_error); ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="email" name="email" required placeholder="Email">
            <input type="password" name="password" required placeholder="Password">
            <button type="submit" name="login">Login</button>
        </form>
        <button class="sign-up-button" onclick="window.location.href='sign_up.php'">Sign Up</button>
        <button class="admin-login-button" onclick="window.location.href='admin_login.php'">Admin Login</button>
    <?php endif; ?>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const likeButtons = document.querySelectorAll('.like-button');

        likeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.dataset.postId;

                // Send a request to like the post
                fetch('like_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ post_id: postId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const likeCounter = this.parentElement.previousElementSibling; // Get the like counter
                        likeCounter.textContent = `${data.new_like_count} Likes`; // Update the counter
                    } else {
                        console.error(data.error); // Log any errors
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        // Handle comment submissions
        const commentButtons = document.querySelectorAll('.comment-button');

        commentButtons.forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.parentElement.querySelector('.like-button').dataset.postId; // Get post ID
                const commentInput = this.parentElement.querySelector('.comment-input'); // Get comment input
                const commentText = commentInput.value; // Get comment text

                if (commentText.trim()) {
                    // Send a request to add a comment
                    fetch('add_comment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ post_id: postId, comment: commentText })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            commentInput.value = ''; // Clear input on success
                            // Optionally, you can update the UI to show the new comment
                            const commentsSection = this.parentElement.parentElement.querySelector('.comments-section'); // Get comments section
                            const newComment = document.createElement('div'); // Create new comment element
                            newComment.classList.add('comment'); // Add comment class
                            newComment.innerHTML = `<strong>${data.commenter_username}:</strong> <span>${commentText}</span>`; // Set inner HTML
                            commentsSection.appendChild(newComment); // Append new comment to the section
                        } else {
                            console.error(data.error); // Log any errors
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    });
    </script>
</body>
</html>
