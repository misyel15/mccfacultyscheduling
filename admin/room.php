<?php
session_start();
include('db_connect.php');
include 'includes/header.php';

// Assuming the user department ID is stored in the session after login
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;

// Function to handle room operations
function handleRoomOperation($conn, $dept_id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $operation = $_POST['operation'] ?? '';
        $id = $_POST['id'] ?? '';
        $room_id = $_POST['room_id'] ?? '';
        $room_name = $_POST['room'] ?? '';

        switch ($operation) {
            case 'save':
                if (empty($id)) {
                    // Insert new room
                    $stmt = $conn->prepare("INSERT INTO roomlist (room_id, room_name, dept_id) VALUES (?, ?, ?)");
                    $stmt->bind_param("ssi", $room_id, $room_name, $dept_id);
                } else {
                    // Update existing room
                    $stmt = $conn->prepare("UPDATE roomlist SET room_id = ?, room_name = ? WHERE id = ? AND dept_id = ?");
                    $stmt->bind_param("ssii", $room_id, $room_name, $id, $dept_id);
                }
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Room " . (empty($id) ? "added" : "updated") . " successfully.";
                } else {
                    $_SESSION['error'] = "Error: " . $stmt->error;
                }
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM roomlist WHERE id = ? AND dept_id = ?");
                $stmt->bind_param("ii", $id, $dept_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Room deleted successfully.";
                } else {
                    $_SESSION['error'] = "Error: " . $stmt->error;
                }
                break;
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Call the function to handle room operations
handleRoomOperation($conn, $dept_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <!-- Include your CSS files here -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
</head>
<body>

<div class="container-fluid" style="margin-top:100px;">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <b>Room List</b>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#roomModal"><i class="fa fa-plus"></i> New Entry</button>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="roomTable">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Room</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $course = $conn->query("SELECT * FROM roomlist WHERE dept_id = '$dept_id' ORDER BY id ASC");
                                while($row = $course->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $row['room_id']; ?></td>
                                    <td>
                                        <p>Room name: <b><?php echo $row['room_name']; ?></b></p>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_room" type="button" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_name']; ?>" data-room_id="<?php echo $row['room_id']; ?>"><i class="fas fa-edit"></i> Edit</button>
                                        <form action="" method="POST" style="display: inline;">
                                            <input type="hidden" name="operation" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button class="btn btn-sm btn-danger delete_room" type="submit" onclick="return confirm('Are you sure you want to delete this room?');"><i class="fas fa-trash-alt"></i> Delete</button>
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
    </div>
</div>

<!-- Room Modal -->
<div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomModalLabel">Room Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="operation" value="save">
                    <input type="hidden" name="id" id="room_id">
                    <div class="form-group">
                        <label for="room_id">Room ID</label>
                        <input type="text" class="form-control" name="room_id" id="room_id_input" required>
                    </div>
                    <div class="form-group">
                        <label for="room">Room Name</label>
                        <input type="text" class="form-control" name="room" id="room_input" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include your JS files here -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#roomTable').DataTable({
        responsive: true
    });

    // Edit Room
    $('.edit_room').click(function() {
        var id = $(this).data('id');
        var room = $(this).data('room');
        var room_id = $(this).data('room_id');

        $('#room_id').val(id);
        $('#room_id_input').val(room_id);
        $('#room_input').val(room);

        $('#roomModal').modal('show');
    });
});
</script>

</body>
</html>