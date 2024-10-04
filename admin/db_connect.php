

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Change these values to match your hosting and database credentials
$host = '127.0.0.1'; // Use the actual hostname (commonly 'localhost' or provided by your hosting service)
$port = '3306'; // Port number, if it's not the default (3306)
$username = 'u510162695_scheduling_db';
$password = '1Scheduling_db';
$database = 'u510162695_scheduling_db';

// Create connection
$conn = new mysqli($host, $username, $password, $database, $port); // Add the port parameter

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}