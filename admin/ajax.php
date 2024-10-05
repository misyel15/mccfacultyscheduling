<?php
class Action {
    private $conn;

    public function __construct() {
        // Initialize database connection (update with your DB details)
        $this->conn = new mysqli("localhost", "username", "password", "database");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function login() {
        // Example login function
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $this->conn->real_escape_string($_POST['username']);
            $password = $this->conn->real_escape_string($_POST['password']);

            // Hash password if necessary (use the same method as during registration)
            $query = "SELECT * FROM users WHERE username = '$username' AND password = MD5('$password')";
            $result = $this->conn->query($query);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['login_id'] = $row['id']; // Store user ID in session
                return 1; // Success
            } else {
                return 0; // Invalid credentials
            }
        }
        return 'No credentials provided'; // Handle case with no credentials
    }

    public function login_faculty() {
        // Faculty login function (similar to login)
        if (isset($_POST['id_no'])) {
            $id_no = $this->conn->real_escape_string($_POST['id_no']);
            $query = "SELECT * FROM faculty WHERE id_no = '$id_no'";
            $result = $this->conn->query($query);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['login_id'] = $row['id']; // Store faculty ID in session
                return 1; // Success
            } else {
                return 0; // Invalid ID
            }
        }
        return 'No ID provided'; // Handle case with no ID
    }

    public function login2() {
        // Implement logic for login2
        // Example:
        return 'login2 functionality not implemented'; 
    }

    public function logout() {
        // Logout functionality
        session_destroy();
        return 'Logged out successfully';
    }

    public function logout2() {
        // Implement logic for logout2
        return 'logout2 functionality not implemented';
    }

    public function save_user() {
        // Implement logic to save a user
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $this->conn->real_escape_string($_POST['username']);
            $password = $this->conn->real_escape_string($_POST['password']);
            // Save user logic
            $query = "INSERT INTO users (username, password) VALUES ('$username', MD5('$password'))";
            if ($this->conn->query($query) === TRUE) {
                return "User saved successfully";
            } else {
                return "Error saving user: " . $this->conn->error;
            }
        }
        return 'No user data provided';
    }

    public function delete_schedule() {
        // Implement logic to delete a schedule
        if (isset($_POST['schedule_id'])) {
            $schedule_id = $this->conn->real_escape_string($_POST['schedule_id']);
            $query = "DELETE FROM schedules WHERE id = '$schedule_id'";
            if ($this->conn->query($query) === TRUE) {
                return "Schedule deleted successfully";
            } else {
                return "Error deleting schedule: " . $this->conn->error;
            }
        }
        return 'No schedule ID provided';
    }

    // Additional methods for other actions can be implemented here
    // ...

    public function __destruct() {
        // Close the database connection
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
