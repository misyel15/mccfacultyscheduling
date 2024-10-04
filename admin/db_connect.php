<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Change these values to match your hosting and database credentials
$host = '3307'; // Or use the provided hostname
$username = 'u510162695_scheduling_db';
$password = '1Scheduling_db';
$database = 'u510162695_scheduling_db';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

