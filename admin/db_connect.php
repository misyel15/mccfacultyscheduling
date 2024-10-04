<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$host = 'localhost'; // Change as needed
$username = 'u510162695_scheduling_db';
$password = '1Scheduling_db';
$database = 'u510162695_scheduling_db';
$port = 3307; // Change if necessary

// Create a new MySQLi object
$conn = new mysqli($host, $username, $password, $database, $port);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
