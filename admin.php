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
    $stmt = $conn->prepare($sql_delete_post);
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Light background color */
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007BFF; /* Blue header */
            color: white;
        }
        .logout-button {
            background-color: red; /* Red background */
            color: black; /* Black text */
            border: 1px solid black; /* Black border */
            padding: 10px 20px; /* Padding for better touch targets */
            border-radius: 5px; /* Round corners */
            cursor: pointer; /* Pointer cursor on hover */
            margin-top: 20px; /* Space above the button */
            display: block; /* Make button a block element */
            width: fit-content; /* Fit the width to the content */
            margin-left: auto; /* Center the button */
            margin-right: auto; /* Center the button */
        }
        .logout-button:hover {
            background-color: darkred; /* Darker red on hover */
        }
    </style>
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
    <table>
        <tr>
            <th>Title</th>
            <th>Content</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result_posts->num_rows > 0) {
            while ($row = $result_posts->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>" . htmlspecialchars($row['content']) . "</td>
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

    <a href="logout.php">
        <button class="logout-button">Logout</button>
    </a>
</body>
</html>

