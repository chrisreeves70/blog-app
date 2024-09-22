<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Index Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Light background color */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full height for centering */
        }
        .styled-box {
            background-color: white; /* White background for the box */
            border: 1px solid #ccc; /* Light border */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow */
            padding: 20px; /* Padding inside the box */
            width: 300px; /* Fixed width for the box */
            text-align: center; /* Center text */
        }
        button {
            background-color: #007BFF; /* Blue button */
            color: white; /* White text */
            border: none; /* No border */
            border-radius: 5px; /* Round corners */
            padding: 10px 15px; /* Padding for buttons */
            cursor: pointer; /* Pointer cursor on hover */
            margin: 5px; /* Space between buttons */
        }
        button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
    </style>
</head>
<body>
    <div class="styled-box">
        <h2>Welcome to Chore Tracker</h2>
        <p>Your application for managing chores efficiently.</p>
        <button onclick="window.location.href='admin_login.php'">Admin Login</button>
        <button onclick="window.location.href='user_dashboard.php'">User Dashboard</button>
        <!-- Add more buttons as needed -->
    </div>
</body>
</html>


