<?php
// Start the session
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: index.php');
    exit;
}

// Database connection
$servername = 'your_database_host'; // Replace with your JawsDB host
$username = 'your_database_username'; // Replace with your JawsDB username
$password = 'your_database_password'; // Replace with your JawsDB password
$dbname = 'your_database_name'; // Replace with your JawsDB database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the database
$sql_users = "SELECT id, username, email FROM users";
$result_users = $conn->query($sql_users);

// Fetch posts from the database
$sql_posts = "SELECT id, title, content FROM posts"; // Assuming you have a 'posts' table
$result_posts = $conn->query($sql_posts);
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
    <?php if ($result_users->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result_users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
                        <form action="delete_user.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>

    <h2>Manage Posts</h2>
    <?php if ($result_posts->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result_posts->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td>
                        <form action="delete_post.php" method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No posts found.</p>
    <?php endif; ?>

    <p><a href="logout.php">Logout</a></p>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
