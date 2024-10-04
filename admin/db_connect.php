<?php
// Database connection parameters
$host = '127.0.0.1'; 
$port = '3306'; 
$username = 'u510162695_scheduling_db';
$password = '1Scheduling_db'; 
$dbname = 'u510162695_scheduling_db';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    // Log error for server-side debugging
    error_log("Connection failed: " . $conn->connect_error);
    
    // Display an alert to the user with JavaScript
    echo "<script type='text/javascript'>
            alert('Connection failed: " . addslashes($conn->connect_error) . "');
          </script>";
    exit(); // Stop the script from executing further
}

// Proceed with your queries after connection is successful
