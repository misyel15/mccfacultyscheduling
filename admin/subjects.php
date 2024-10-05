<?php
session_start(); // Start the session
include('db_connect.php'); // Include your database connection
include 'includes/header.php'; // Include your header file

// Assuming the user department ID is stored in the session after login
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'save_subject') {
            save_subject($conn, $dept_id);
        } elseif ($_POST['action'] === 'delete_subject') {
            delete_subject($conn);
        }
    }
}

function save_subject($conn, $dept_id) {
    if (isset($_POST['subject'], $_POST['description'], $_POST['Lec_Units'], $_POST['Lab_Units'], $_POST['hours'], $_POST['total_units'], $_POST['course'], $_POST['year'], $_POST['semester'], $_POST['specialization'])) {
        $subject = $conn->real_escape_string($_POST['subject']);
        $description = $conn->real_escape_string($_POST['description']);
        $Lec_Units = $conn->real_escape_string($_POST['Lec_Units']);
        $Lab_Units = $conn->real_escape_string($_POST['Lab_Units']);
        $hours = $conn->real_escape_string($_POST['hours']);
        $total_units = $conn->real_escape_string($_POST['total_units']);
        $course = $conn->real_escape_string($_POST['course']);
        $year = $conn->real_escape_string($_POST['year']);
        $semester = $conn->real_escape_string($_POST['semester']);
        $specialization = $conn->real_escape_string($_POST['specialization']);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        // Check for duplicate subjects
        $check_duplicate = $conn->query("SELECT * FROM subjects WHERE subject = '$subject' AND id != '$id'");
        if ($check_duplicate->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Duplicate subject found.']);
            return;
        }

        $data = "subject = '$subject', 
                 description = '$description', 
                 Lec_units = '$Lec_Units', 
                 Lab_units = '$Lab_Units', 
                 hours = '$hours', 
                 total_units = '$total_units', 
                 course = '$course', 
                 year = '$year', 
                 semester = '$semester', 
                 specialization = '$specialization', 
                 dept_id = '$dept_id'";

        // Insert or update based on the presence of an ID
        if (empty($id)) {
            $save = $conn->query("INSERT INTO subjects SET $data");
        } else {
            $save = $conn->query("UPDATE subjects SET $data WHERE id = $id");
        }

        if ($save) {
            echo json_encode(['status' => 'success', 'message' => 'Subject saved successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save subject. ' . $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing.']);
    }
}

function delete_subject($conn) {
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $delete = $conn->query("DELETE FROM subjects WHERE id = $id");
        if ($delete) {
            echo json_encode(['status' => 'success', 'message' => 'Subject deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete subject.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID is required.']);
    }
}
?>

<!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Include DataTables CSS (optional) -->
<link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Include DataTables JS (optional) -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<div class="container-fluid" style="margin-top:100px;">
    <div class="col-lg-14">
        <div class="row">
            <!-- Table Panel -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <b>Subject List</b>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#subjectModal">
                            <i class="fa fa-user-plus"></i> New Entry
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="subjectTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Subject</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    // Fetch subjects based on department ID
                                    $subject = $dept_id ? 
                                        $conn->query("SELECT * FROM subjects WHERE dept_id = '$dept_id' ORDER BY id ASC") : 
                                        $conn->query("SELECT * FROM subjects ORDER BY id ASC");
                                    
                                    while ($row = $subject->fetch_assoc()): ?>
                                    <tr class="subject-row">
                                        <td class="text-center"><?php echo $i++; ?></td>
                                        <td>
                                            <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
                                            <p><small><b>Description:</b> <?php echo $row['description']; ?></small></p>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary edit_subject" type="button" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-subject="<?php echo htmlspecialchars($row['subject'], ENT_QUOTES); ?>" 
                                                data-description="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>" 
                                                data-units="<?php echo $row['total_units']; ?>" 
                                                data-leccount="<?php echo $row['Lec_units']; ?>" 
                                                data-labcount="<?php echo $row['Lab_units']; ?>" 
                                                data-hours="<?php echo $row['hours']; ?>" 
                                                data-course="<?php echo htmlspecialchars($row['course'], ENT_QUOTES); ?>" 
                                                data-year="<?php echo $row['year']; ?>" 
                                                data-semester="<?php echo $row['semester']; ?>" 
                                                data-specialization="<?php echo htmlspecialchars($row['specialization'], ENT_QUOTES); ?>" 
                                                data-toggle="modal" data-target="#subjectModal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete_subject" type="button" data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
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

            <!-- Modal -->
            <div class="modal fade" id="subjectModal" tabindex="-1" role="dialog" aria-labelledby="subjectModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="subjectModalLabel">Subject Form</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="manage-subject">
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                <div class="form-group">
                                    <label class="control-label">Subject</label>
                                    <input type="text" class="form-control" name="subject" id="subject" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Lecture Units</label>
                                    <input type="number" class="form-control" name="Lec_Units" id="Lec_Units" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Lab Units</label>
                                    <input type="number" class="form-control" name="Lab_Units" id="Lab_Units" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Hours</label>
                                    <input type="number" class="form-control" name="hours" id="hours" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Total Units</label>
                                    <input type="number" class="form-control" name="total_units" id="total_units" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Course</label>
                                    <input type="text" class="form-control" name="course" id="course" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Year</label>
                                    <input type="text" class="form-control" name="year" id="year" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Semester</label>
                                    <input type="text" class="form-control" name="semester" id="semester" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Specialization</label>
                                    <input type="text" class="form-control" name="specialization" id="specialization" required>
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
            <!-- End Modal -->
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#subjectTable').DataTable();

    // Handle edit subject button click
    $('.edit_subject').on('click', function () {
        const id = $(this).data('id');
        const subject = $(this).data('subject');
        const description = $(this).data('description');
        const units = $(this).data('units');
        const leccount = $(this).data('leccount');
        const labcount = $(this).data('labcount');
        const hours = $(this).data('hours');
        const course = $(this).data('course');
        const year = $(this).data('year');
        const semester = $(this).data('semester');
        const specialization = $(this).data('specialization');

        $('#id').val(id);
        $('#subject').val(subject);
        $('#description').val(description);
        $('#Lec_Units').val(leccount);
        $('#Lab_Units').val(labcount);
        $('#hours').val(hours);
        $('#total_units').val(units);
        $('#course').val(course);
        $('#year').val(year);
        $('#semester').val(semester);
        $('#specialization').val(specialization);
        $('#subjectModalLabel').text('Edit Subject');
    });

    // Handle save subject form submission
    $('#manage-subject').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save_subject';
        $.ajax({
            url: '', // Current page URL
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                Swal.fire({
                    icon: response.status === 'success' ? 'success' : 'error',
                    title: response.status === 'success' ? 'Success' : 'Error',
                    text: response.message,
                }).then(() => {
                    if (response.status === 'success') {
                        location.reload();
                    }
                });
            }
        });
    });

    // Handle delete subject button click
    $('.delete_subject').on('click', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '', // Current page URL
                    method: 'POST',
                    data: { id: id, action: 'delete_subject' },
                    dataType: 'json',
                    success: function (response) {
                        Swal.fire({
                            icon: response.status === 'success' ? 'success' : 'error',
                            title: response.status === 'success' ? 'Deleted!' : 'Error',
                            text: response.message,
                        }).then(() => {
                            if (response.status === 'success') {
                                location.reload();
                            }
                        });
                    }
                });
            }
        });
    });
});
</script>

