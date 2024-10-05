<?php
session_start(); // Start the session
include('db_connect.php');
include 'includes/header.php';

// Assuming the user department ID is stored in the session after login
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'save_subject') {
        save_subject($conn, $dept_id);
    } elseif ($_POST['action'] === 'delete_subject') {
        delete_subject($conn);
    }
}

function save_subject($conn, $dept_id) {
    if (isset($_POST['subject'], $_POST['description'], $_POST['Lec_Units'], $_POST['Lab_Units'], $_POST['hours'], $_POST['total_units'], $_POST['course'], $_POST['year'], $_POST['semester'], $_POST['specialization'])) {
        extract($_POST);

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

        $check_duplicate = $conn->query("SELECT * FROM subjects WHERE subject = '$subject' AND id != '$id'");
        if ($check_duplicate->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Duplicate subject found.']);
            return;
        }

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
        $id = $_POST['id'];
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
                                    if ($dept_id) {
                                        $subject = $conn->query("SELECT * FROM subjects WHERE dept_id = '$dept_id' ORDER BY id ASC");
                                    } else {
                                        $subject = $conn->query("SELECT * FROM subjects ORDER BY id ASC");
                                    }
                                    while($row = $subject->fetch_assoc()):
                                    ?>
                                    <tr class="subject-row">
                                        <td class="text-center"><?php echo $i++; ?></td>
                                        <td>
                                            <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
                                            <p><small><b>Description:</b> <?php echo $row['description']; ?></small></p>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary edit_subject" type="button" data-id="<?php echo $row['id']; ?>" data-subject="<?php echo $row['subject']; ?>" data-description="<?php echo $row['description']; ?>" data-toggle="modal" data-target="#subjectModal">
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
                                    <label class="control-label">Total Units</label>
                                    <input type="number" class="form-control" name="total_units" id="total_units" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Lec Units</label>
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
                                    <label class="control-label">Course</label>
                                    <select class="form-control" name="course" id="course" required>
                                        <option value="">Select Course</option>
                                        <?php 
                                            $sql = "SELECT * FROM courses";
                                            $query = $conn->query($sql);
                                            while($row = $query->fetch_array()):
                                                $course = $row['course'];
                                        ?>
                                        <option value="<?php echo $course; ?>"><?php echo ucwords($course); ?></option>
                                        <?php endwhile; ?>
                                    </select>
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
                                    <input type="text" class="form-control" name="specialization" id="specialization">
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
$(document).ready(function() {
    // Initialize DataTable
    $('#subjectTable').DataTable();

    // Handle form submission
    $('#manage-subject').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        const formData = $(this).serialize(); // Serialize the form data
        formData += '&action=save_subject'; // Append the action

        $.ajax({
            url: '', // Current page
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    icon: response.status === 'success' ? 'success' : 'error',
                    title: response.status === 'success' ? 'Success' : 'Error',
                    text: response.message,
                }).then(() => {
                    location.reload(); // Reload the page to see changes
                });
            },
            error: function() {
                Swal.fire('Error', 'There was an error processing your request.', 'error');
            }
        });
    });

    // Edit subject
    $(document).on('click', '.edit_subject', function() {
        $('#id').val($(this).data('id'));
        $('#subject').val($(this).data('subject'));
        $('#description').val($(this).data('description'));
        $('#total_units').val($(this).data('units'));
        $('#Lec_Units').val($(this).data('leccount'));
        $('#Lab_Units').val($(this).data('labcount'));
        $('#hours').val($(this).data('hours'));
        $('#course').val($(this).data('course'));
        $('#year').val($(this).data('year'));
        $('#semester').val($(this).data('semester'));
        $('#specialization').val($(this).data('specialization'));
    });

    // Delete subject
    $(document).on('click', '.delete_subject', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this subject!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '', // Current page
                    method: 'POST',
                    data: { id: id, action: 'delete_subject' },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'There was an error processing your request.', 'error');
                    }
                });
            }
        });
    });
});
</script>

