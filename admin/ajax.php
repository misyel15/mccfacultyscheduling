<?php
session_start();
include('db_connect.php'); // Ensure your database connection is included here

if (isset($_GET['action']) && $_GET['action'] === 'save_room') {
    save_room();
}

function save_room() {
    global $conn;
    $dept_id = $_SESSION['dept_id']; // Get department ID from the session
    extract($_POST);

    // Sanitize and validate inputs
    $room = $conn->real_escape_string(trim($room));
    $room_id = $conn->real_escape_string(trim($room_id));

    // Check for duplicates in the same department
    $check = $conn->query("SELECT * FROM roomlist WHERE (room_name = '$room' OR room_id = '$room_id') AND dept_id = '$dept_id'");
    
    if ($check->num_rows > 0) {
        echo 3; // Duplicate entry
        return;
    }

    $data = "room_name = '$room', room_id = '$room_id', dept_id = '$dept_id'";

    if (empty($id)) {
        // Insert new room
        $save = $conn->query("INSERT INTO roomlist SET $data");
        echo $save ? 1 : 0; // Return 1 if successful, else 0
    } else {
        // Update existing room
        $save = $conn->query("UPDATE roomlist SET $data WHERE id = " . intval($id));
        echo $save ? 2 : 0; // Return 2 if successful, else 0
    }
}
?>
