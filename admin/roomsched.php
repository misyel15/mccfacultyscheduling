<?php 
include('db_connect.php');

// Handle the AJAX request to fetch schedule data
if (isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];

    // Fetch schedule based on room_id
    $stmt = $conn->prepare("SELECT * FROM loading WHERE rooms = ? ORDER BY timeslot ASC");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $output = '';
        while ($row = $result->fetch_assoc()) {
            $time = htmlspecialchars($row['timeslot']);
            $monday = isset($row['course']) ? htmlspecialchars($row['course']) : '';
            $tuesday = isset($row['Tuesday']) ? htmlspecialchars($row['Tuesday']) : '';
            $wednesday = isset($row['Wednesday']) ? htmlspecialchars($row['Wednesday']) : '';
            $thursday = isset($row['Thursday']) ? htmlspecialchars($row['Thursday']) : '';
            $friday = isset($row['Friday']) ? htmlspecialchars($row['Friday']) : '';
            $saturday = isset($row['Saturday']) ? htmlspecialchars($row['Saturday']) : '';

            // Append rows with the sub-descriptions for each day
            $output .= '<tr>
                <td class="text-center">' . $time . '</td>
                <td class="text-center">' . $monday . '</td>
                <td class="text-center">' . $tuesday . '</td>
                <td class="text-center">' . $wednesday . '</td>
                <td class="text-center">' . $thursday . '</td>
                <td class="text-center">' . $friday . '</td>
                <td class="text-center">' . $saturday . '</td>
            </tr>';
        }
        echo $output;
    } else {
        echo '<tr><td colspan="7" class="text-center">No schedule found.</td></tr>';
    }
    $stmt->close();
    exit();
}
?>

<?php
session_start();
include('db_connect.php');
include 'includes/header.php';

// Assuming you store the department ID in the session during login
// Example: $_SESSION['dept_id'] = $user['dept_id'];
$dept_id = $_SESSION['dept_id']; // Get the department ID from the session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schedule Load</title>
    <style>
        @media (max-width: 768px) {
            .card-header {
                text-align: center;
            }
            .table thead th {
                font-size: 12px;
            }
            .table td {
                font-size: 12px;
            }
            .modal-dialog {
                max-width: 90%;
                margin: 1.75rem auto;
            }
        }
        td {
            vertical-align: middle !important;
        }
    </style>
</head>
<body>
<div class="container-fluid" style="margin-top:100px;">
    <!-- Table Panel -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <center><h3>Room Schedule's Load</h3></center>
                </div>
                <div class="card-body">
                    <div class="row">
                        <label for="" class="control-label col-md-2 offset-md-2">View Loads of:</label>
                        <div class="col-md-4">
                            <select name="room_name" id="room_name" class="custom-select select2" onchange="fetchRoomSchedule(this.value)">
                                <option value="">Select Room</option>
                                <?php
                                $stmt = $conn->prepare("SELECT id, room_name FROM roomlist ORDER BY id ASC");
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($row['id']) . '">' . ucwords(htmlspecialchars($row['room_name'])) . '</option>';
                                    }
                                } else {
                                    echo 'Error: ' . $conn->error;
                                }
                                $stmt->close();
                                ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="insloadtable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="100px">Time</th>
                                    <th class="text-center">Monday</th>
                                    <th class="text-center">Tuesday</th>
                                    <th class="text-center">Wednesday</th>
                                    <th class="text-center">Thursday</th>
                                    <th class="text-center">Friday</th>
                                    <th class="text-center">Saturday</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Schedule data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery, Bootstrap JS, and your custom JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
function fetchRoomSchedule(roomId) {
    if(roomId) {
        $.ajax({
            url: '', // Current page
            type: 'POST',
            data: { room_id: roomId },
            success: function(response) {
                $('#insloadtable tbody').html(response); // Update table body with response data
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });
    } else {
        $('#insloadtable tbody').html(''); // Clear table if no room is selected
    }
}
</script>
</body>
</html>
