<?php
session_start(); // Start the session
include 'config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute SQL statement to find user by email
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id; // Store user ID in session
            echo "Login successful!";
            // Redirect to dashboard or homepage
        } else {
            echo "Invalid password."; // Incorrect password message
        }
    } else {
        echo "No user found with that email."; // No user message
    }

    $stmt->close(); // Close statement
    $conn->close(); // Close database connection
}
?>
