<?php
// Define the database connection parameters
$MySQL_username = 'u510162695_scheduling_db';
$Password = '1Scheduling_db';
$MySQL_database_name = 'u510162695_scheduling_db';

// Create a new MySQLi object
$conn = new mysqli('localhost', $MySQL_username, $Password, $MySQL_database_name);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
