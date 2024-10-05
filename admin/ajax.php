<?php
ob_start();
session_start(); // Ensure session is started
$action = isset($_GET['action']) ? $_GET['action'] : '';
include 'admin_class.php';
$crud = new Action();

// Debugging: Log the action
error_log("Action: $action");

$response = ''; // Initialize response variable

switch ($action) {
    case 'login':
        $response = $crud->login();
        break;

    case 'login_faculty':
        $response = $crud->login_faculty();
        break;

    case 'login2':
        $response = $crud->login2();
        break;

    case 'logout':
        $response = $crud->logout();
        break;

    case 'logout2':
        $response = $crud->logout2();
        break;

    case 'save_user':
        $response = $crud->save_user();
        break;

    // Add similar cases for all other actions
    // ...

    case 'delete_schedule':
        $response = $crud->delete_schedule();
        break;

    default:
        $response = 'Invalid action'; // Handle unknown action
        break;
}

// Only echo the response if it's set
if ($response) {
    echo $response;
}

ob_end_flush();
?>
