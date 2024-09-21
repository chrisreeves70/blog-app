<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="login_process.php" method="post">
        <!-- Email input -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <!-- Password input -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <!-- Submit button -->
        <button type="submit">Login</button>
    </form>
</body>
</html>
