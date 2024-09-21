<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
</head>
<body>
    <h2>Create Post</h2>
    <form action="create_post_process.php" method="post">
        <!-- Title input -->
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <!-- Content input -->
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>

        <!-- Submit button -->
        <button type="submit">Create Post</button>
    </form>
</body>
</html>
