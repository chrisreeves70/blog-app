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
            background-color: #FF4500 !important; /* Red for logout button with !important to override other styles */
            color: white !important; /* White text color with !important */
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
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p style="text-align: center;"><?php echo htmlspecialchars($post['content']); ?></p>
            </div>
        <?php endforeach; ?>

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

