<?php
// Include your database connection here
include 'config.php';

// Delete a user if the delete_user_id is set
if (isset($_POST['delete_user_id'])) {
    $delete_user_id = $_POST['delete_user_id'];
    $sql_delete_user = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql_delete_user);
    $stmt->bind_param("i", $delete_user_id);
    if ($stmt->execute()) {
        echo "User deleted successfully!";
        header("Location: admin.php"); // Redirect to refresh the page
        exit;
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}

// Delete a post if the delete_post_id is set
if (isset($_POST['delete_post_id'])) {
    $delete_post_id = $_POST['delete_post_id'];
    $sql_delete_post = "DELETE FROM posts WHERE id = ?";
    $stmt->bind_param("i", $delete_post_id);
    if ($stmt->execute()) {
        echo "Post deleted successfully!";
        header("Location: admin.php"); // Redirect to refresh the page
        exit;
    } else {
        echo "Error deleting post: " . $conn->error;
    }
}

// Fetch all users
$sql_users = "SELECT * FROM users";
$result_users = $conn->query($sql_users);

// Fetch all posts
$sql_posts = "SELECT * FROM posts";
$result_posts = $conn->query($sql_posts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script>
        // JavaScript confirmation function for user deletion
        function confirmDeleteUser() {
            return confirm("Are you sure you want to delete this user?");
        }

        // JavaScript confirmation function for post deletion
        function confirmDeletePost() {
            return confirm("Are you sure you want to delete this post?");
        }
    </script>
</head>
<body>
    <h1>Admin Panel</h1>

    <!-- List of Users -->
    <h2>Manage Users</h2>
    <table border="1">
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result_users->num_rows > 0) {
            while ($row = $result_users->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['username'] . "</td>
                        <td>" . $row['email'] . "</td>
                        <td>
                            <form method='POST' action='admin.php' onsubmit='return confirmDeleteUser();'>
                                <input type='hidden' name='delete_user_id' value='" . $row['id'] . "'>
                                <input type='submit' value='Delete User'>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No users found</td></tr>";
        }
        ?>
    </table>

    <!-- List of Posts -->
    <h2>Manage Posts</h2>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Content</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result_posts->num_rows > 0) {
            while ($row = $result_posts->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['title'] . "</td>
                        <td>" . $row['content'] . "</td>
                        <td>
                            <form method='POST' action='admin.php' onsubmit='return confirmDeletePost();'>
                                <input type='hidden' name='delete_post_id' value='" . $row['id'] . "'>
                                <input type='submit' value='Delete Post'>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No posts found</td></tr>";
        }
        ?>
    </table>

    <!-- Back to Login Button -->
    <br><br>
    <a href="login.php">
        <button>Back to Login</button>
    </a>

</body>
</html>

