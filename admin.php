<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Fetch users
$sql_users = "SELECT id, username, email FROM users";
$result_users = $conn->query($sql_users);

// Fetch posts
$sql_posts = "SELECT id, title FROM posts"; // Adjust if you have a different column
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
    <?php if ($result_users && $result_users->num_rows > 0): ?>
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
                        <form action="delete_user.php" method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>

    <h2>Manage Posts</h2>
    <?php if ($result_posts && $result_posts->num_rows > 0): ?>
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
                        <form action="delete_post.php" method="POST" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No posts found.</p>
    <?php endif; ?>

    <a href="index.php">Back to Homepage</a>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
