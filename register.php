<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register_process.php" method="post">
        <!-- Username input -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <!-- Email input -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <!-- Password input -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <!-- Submit button -->
        <button type="submit">Register</button>
    </form>
</body>
</html>
