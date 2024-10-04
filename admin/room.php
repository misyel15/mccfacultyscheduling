<?php
session_start(); // Start the session
include('db_connect.php');
include 'includes/header.php';

// Assuming the user department ID is stored in the session after login
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Save Room
        if ($_POST['action'] === 'save_room') {
            $room_id = $_POST['room_id'];
            $room_name = $_POST['room'];

            // Check if room already exists
            $checkQuery = $conn->query("SELECT * FROM roomlist WHERE room_id = '$room_id' OR room_name = '$room_name' AND dept_id = '$dept_id'");
            if ($checkQuery->num_rows > 0) {
                $message = "Room already exists"; // Store error message
            } else {
                // Insert new room
                $conn->query("INSERT INTO roomlist (room_id, room_name, dept_id) VALUES ('$room_id', '$room_name', '$dept_id')");
                $message = "Room successfully added"; // Store success message
            }
        }

        // Update Room
        if ($_POST['action'] === 'update_room') {
            $id = $_POST['id'];
            $room_id = $_POST['room_id'];
            $room_name = $_POST['room'];

            // Update room details
            $conn->query("UPDATE roomlist SET room_id = '$room_id', room_name = '$room_name' WHERE id = '$id'");
            $message = "Room successfully updated"; // Store success message
        }

        // Delete Room
        if ($_POST['action'] === 'delete_room') {
            $id = $_POST['id'];
            $conn->query("DELETE FROM roomlist WHERE id = '$id'");
            $message = "Room successfully deleted"; // Store success message
        }
    }
}

// Fetch room list after form submission
$rooms = $conn->query("SELECT * FROM roomlist WHERE dept_id = '$dept_id' ORDER BY id ASC");
?>

<!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid" style="margin-top:100px;">
    <div class="row">
        <!-- FORM Panel -->
        <div class="col-md-4">
            <!-- Modal -->
            <div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="roomModalLabel">Room Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" id="manage-room">
                                <input type="hidden" name="id">
                                <input type="hidden" name="action" id="form-action" value="save_room">
                                <div class="form-group mb-3">
                                    <label class="form-label">Room ID</label>
                                    <input type="text" class="form-control" name="room_id" id="room_id" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Room</label>
                                    <input type="text" class="form-control" name="room" id="room" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="saveRoomBtn">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
        </div>
        <!-- FORM Panel -->

        <!-- Table Panel -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <b>Room List</b>
                    <button class="btn btn-primary btn-sm" id="newEntryBtn"><i class="fa fa-user-plus"></i> New Entry</button>
                </div>
                <div class="card-body">
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
                                while($row = $rooms->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?php echo $row['room_id']; ?></td>
                                    <td>
                                        <p>Room name: <b><?php echo $row['room_name']; ?></b></p>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_room" type="button" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_name']; ?>" data-room_id="<?php echo $row['room_id']; ?>"><i class="fas fa-edit"></i> Edit</button>
                                        <button class="btn btn-sm btn-danger delete_room" type="button" data-id="<?php echo $row['id']; ?>"><i class="fas fa-trash-alt"></i> Delete</button>
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

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#roomTable').DataTable({
        responsive: true
    });

    // Show the modal when clicking the "New Entry" button
    $('#newEntryBtn').click(function() {
        $('#roomModal').modal('show');
        $('#form-action').val('save_room'); // Set action to save
        $('#manage-room').get(0).reset(); // Reset form
    });

    // Save Room
    $('#saveRoomBtn').click(function() {
        $('#manage-room').submit();
    });

    // Edit Room
    $('.edit_room').click(function() {
        var cat = $('#manage-room');
        cat.get(0).reset();
        cat.find("[name='id']").val($(this).attr('data-id'));
        cat.find("[name='room']").val($(this).attr('data-room'));
        cat.find("[name='room_id']").val($(this).attr('data-room_id'));
        $('#form-action').val('update_room'); // Set action to update
        $('#roomModal').modal('show');
    });

    // Delete Room
    $('.delete_room').click(function() {
        var id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this data!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                delete_room(id);
            }
        });
    });

    function delete_room(id) {
        $.post('', { action: 'delete_room', id: id }, function(resp) {
            if (resp == 1) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Room data successfully deleted.',
                    showConfirmButton: true
                }).then(function() {
                    location.reload(); // Refresh the page to show updated room list
                });
            }
        });
    }
});
</script>

<?php if (isset($message)): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?php echo $message; ?>',
        confirmButtonText: 'OK'
    });
</script>
<?php endif; ?>
