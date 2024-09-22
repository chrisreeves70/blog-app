<?php
session_start();
require 'config.php'; // Include the database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch users from the database
$result = $conn->query("SELECT * FROM users");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h1>Admin Panel</h1>
    <h2>Manage Users</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <form method="POST" action="delete_user.php">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Manage Posts</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM posts");
        $posts = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($posts as $post): ?>
            <tr>
                <td><?php echo htmlspecialchars($post['id']); ?></td>
                <td><?php echo htmlspecialchars($post['title']); ?></td>
                <td>
                    <form method="POST" action="delete_post.php">
                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this post?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Buttons to go back to the main page and logout -->
    <button onclick="window.location.href='index.php'">Back to Main Page</button>
    <form method="POST" action="logout.php" style="display:inline;">
        <button type="submit">Logout</button>
    </form>
</body>
</html>

