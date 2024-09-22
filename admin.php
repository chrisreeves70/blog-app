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
        header("Location: admin.php"); // Redirect to refresh the page
        exit;
    } else {
        echo "<p style='color:red;'>Error deleting user: " . $conn->error . "</p>";
    }
}

// Delete a post if the delete_post_id is set
if (isset($_POST['delete_post_id'])) {
    $delete_post_id = $_POST['delete_post_id'];
    $sql_delete_post = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql_delete_post);
    $stmt->bind_param("i", $delete_post_id);
    if ($stmt->execute()) {
        header("Location: admin.php"); // Redirect to refresh the page
        exit;
    } else {
        echo "<p style='color:red;'>Error deleting post: " . $conn->error . "</p>";
    }
}

// Fetch all users
$sql_users = "SELECT * FROM users";
$result_users = $conn->query($sql_users);

// Fetch all posts
$sql_posts = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id";
$result_posts = $conn->query($sql_posts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" type="text/css" href="styles.css"> <!-- Link to your existing styles -->
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            margin: 20px auto;
            width: 80%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        h1, h2 {
            color: #333;
        }
        button {
            background-color: #90EE90; /* Light green */
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            color: black;
        }
        button:hover {
            background-color: #76c776; /* Darker green on hover */
        }
    </style>
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
    <table>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result_users->num_rows > 0) {
            while ($row = $result_users->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>
                            <form method='POST' action='admin.php' onsubmit='return confirmDeleteUser();'>
                                <input type='hidden' name='delete_user_id' value='" . htmlspecialchars($row['id']) . "'>
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
    <table>
        <tr>
            <th>Title</th>
            <th>Content</th>
            <th>Author</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result_posts->num_rows > 0) {
            while ($row = $result_posts->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>" . htmlspecialchars($row['content']) . "</td>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>
                            <form method='POST' action='admin.php' onsubmit='return confirmDeletePost();'>
                                <input type='hidden' name='delete_post_id' value='" . htmlspecialchars($row['id']) . "'>
                                <input type='submit' value='Delete Post'>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No posts found</td></tr>";
        }
        ?>
    </table>

    <a href="logout.php">
        <button>Logout</button>
    </a>

</body>
</html>

