<?php
// Database connection settings for JawsDB
$host = 'jj820qt5lpu6krut.cbetxkdyhwsb.us-east-1.rds.amazonaws.com'; // JawsDB host
$username = 'o9fkjfrbhuna7i61'; // JawsDB username
$password = 'ehv2ecqmxp1cpjy6'; // JawsDB password
$dbname = 'o0gsqsrnl17sb6rh'; // JawsDB database name

// Set session cookie lifetime to 0 so it expires when the browser closes
ini_set('session.cookie_lifetime', 0);
session_start(); // Start the session

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
