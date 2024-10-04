<?php
// Define the database connection parameters
$host = '3307';
$username = 'u510162695_scheduling_db';
$password = '1Scheduling_db';
$database = 'u510162695_scheduling_db';

// Create a new MySQLi object
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
