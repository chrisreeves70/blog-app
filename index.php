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
        }
        .author {
            background-color: #007BFF; /* Blue background for author rectangle */
            color: white; /* White text color */
            padding: 5px; /* Padding for better appearance */
            border-radius: 5px; /* Round corners */
            display: inline-block; /* Inline block for rectangle */
            margin-bottom: 10px; /* Space below the author box */
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
            background-color: #90EE90; /* Muted light green for login button */
        }
        .small-button {
            width: 25%; /* Set a smaller width for the other buttons */
        }
        .sign-up-button {
            background-color: #007BFF; /* Muted blue for sign-up button */
        }
        .admin-login-button {
            background-color: #FF4500; /* Muted red for admin login button */
        }
    </style>
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <h2>Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <!-- Author username displayed in a rectangle -->
                <div class="author"><?php echo htmlspecialchars($post['username']); ?></div>
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
            </div>
        <?php endforeach; ?>

        <!-- Logout button -->
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

        <!-- Sign up and Admin login buttons -->
        <button class="small-button sign-up-button" onclick="window.location.href='register.php'">Not a User? Sign Up</button>
        <button class="small-button admin-login-button" onclick="window.location.href='admin_login.php'">Admin Login</button>
    <?php endif; ?>
</body>
</html>
