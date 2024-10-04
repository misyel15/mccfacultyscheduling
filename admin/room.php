<?php
session_start();
include('db_connect.php'); // Ensure you have your database connection

// Check if the user is logged in and has a valid department ID
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;
if (is_null($dept_id)) {
    die("Invalid department ID");
}

// Handle form submissions for adding or updating rooms
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $room_id = trim($_POST['room_id']);
    $room = trim($_POST['room']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    // Prevent SQL Injection
    $room = $conn->real_escape_string($room);
    $room_id = $conn->real_escape_string($room_id);

    // Check for duplicates
    $check = $conn->query("SELECT * FROM roomlist WHERE (room_name = '$room' OR room_id = '$room_id') AND dept_id = '$dept_id'" . ($id ? " AND id != $id" : ""));
    
    if ($check->num_rows > 0) {
        $error = "Room name or ID already exists.";
    } else {
        if (empty($id)) {
            // Insert new room
            $save = $conn->query("INSERT INTO roomlist (room_name, room_id, dept_id) VALUES ('$room', '$room_id', '$dept_id')");
            $success = $save ? "Room successfully added." : "Failed to add room.";
        } else {
            // Update existing room
            $save = $conn->query("UPDATE roomlist SET room_name = '$room', room_id = '$room_id' WHERE id = $id");
            $success = $save ? "Room successfully updated." : "Failed to update room.";
        }
    }
}

// Fetch room list
$courses = $conn->query("SELECT * FROM roomlist WHERE dept_id = '$dept_id' ORDER BY id ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
</head>
<body>
<div class="container-fluid" style="margin-top: 100px;">
    <div class="row">
        <!-- FORM Panel -->
        <div class="col-md-4">
            <button class="btn btn-primary" data-toggle="modal" data-target="#roomModal">New Room</button>

            <!-- Modal -->
            <div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="roomModalLabel">Room Form</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id" id="roomId">
                                <div class="form-group mb-3">
                                    <label class="form-label">Room ID</label>
                                    <input type="text" class="form-control" name="room_id" id="room_id" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Room Name</label>
                                    <input type="text" class="form-control" name="room" id="room" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
        </div>
        <!-- FORM Panel -->

        <!-- Table Panel -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <b>Room List</b>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="roomTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Room ID</th>
                                    <th class="text-center">Room Name</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = $courses->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?php echo $row['room_id']; ?></td>
                                    <td><?php echo $row['room_name']; ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_room" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_name']; ?>" data-room_id="<?php echo $row['room_id']; ?>">Edit</button>
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table Panel -->
    </div>    
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#roomTable').DataTable();

    // Handle the Edit button click
    $('.edit_room').click(function() {
        var id = $(this).data('id');
        var room = $(this).data('room');
        var room_id = $(this).data('room_id');
        
        $('#roomId').val(id);
        $('#room_id').val(room_id);
        $('#room').val(room);
        $('#roomModal').modal('show');
    });
});
</script>

</body>
</html>
