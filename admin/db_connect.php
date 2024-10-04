<?php
// Enable detailed error reporting (for development purposes only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection variables
$host = '127.0.0.1'; // Replace with actual hostname or 'localhost'
$port = '3306';      // Default MySQL port (adjust if necessary)
$username = 'u510162695_scheduling_db'; // Your database username
$password = '1Scheduling_db';           // Your database password
$database = 'u510162695_scheduling_db'; // Your database name

// Create the connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check the connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error . ' (' . $conn->connect_errno . ')');
} else {
    echo 'Database connected successfully.';
}

// Use the connection ($conn) for your queries
