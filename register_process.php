<?php
include 'config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

    // Prepare and execute SQL statement to insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    // Check if registration was successful
    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error; // Output any error
    }

    $stmt->close(); // Close statement
    $conn->close(); // Close database connection
}
?>
