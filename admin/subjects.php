<?php
session_start(); // Start the session
include('db_connect.php');
include 'includes/header.php';

// Assuming the user department ID is stored in the session after login
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'save_subject') {
            save_subject($conn, $dept_id);
        } elseif ($_POST['action'] == 'delete_subject') {
            delete_subject($conn);
        }
        exit; // End the script after handling the request
    }
}

function save_subject($conn, $dept_id) {
    extract($_POST);
    // Build the data string with dept_id included
    $data = "subject = '$subject', ";
    $data .= "description = '$description', ";
    $data .= "Lec_units = '$Lec_Units', ";
    $data .= "Lab_units = '$Lab_Units', ";
    $data .= "hours = '$hours', ";
    $data .= "total_units = '$total_units', ";
    $data .= "course = '$course', ";
    $data .= "year = '$year', ";
    $data .= "semester = '$semester', ";
    $data .= "specialization = '$specialization', ";
    $data .= "dept_id = '$dept_id' "; // Add dept_id to the data string

    // Check for duplicate subject
    $check_duplicate = $conn->query("SELECT * FROM subjects WHERE subject = '$subject' AND id != '$id'");
    if ($check_duplicate->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Duplicate subject found.']);
        return;
    }

    if (empty($id)) {
        // Insert new subject
        $save = $conn->query("INSERT INTO subjects SET $data");
    } else {
        // Update existing subject
        $save = $conn->query("UPDATE subjects SET $data WHERE id = $id");
    }

    if ($save) {
        echo json_encode(['status' => 'success', 'message' => 'Subject saved successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save subject.']);
    }
}

function delete_subject($conn) {
    extract($_POST);
    $delete = $conn->query("DELETE FROM subjects WHERE id = " . (int)$id);
    if ($delete) {
        echo json_encode(['status' => 'success', 'message' => 'Subject deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete subject.']);
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

<script>
$(document).ready(function() {
    // Save subject
    $('#manage-subject').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        $.ajax({
            url: '', // Current page
            method: 'POST',
            data: $(this).serialize() + '&action=save_subject',
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    icon: response.status === 'success' ? 'success' : 'error',
                    title: response.status === 'success' ? 'Success' : 'Error',
                    text: response.message,
                }).then(() => {
                    location.reload(); // Reload the page to see changes
                });
            }
        });
    });

    // Delete subject
    $('.delete_subject').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: { id: id, action: 'delete_subject' },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: response.status === 'success' ? 'success' : 'error',
                            title: response.status === 'success' ? 'Deleted!' : 'Error',
                            text: response.message,
                        }).then(() => {
                            location.reload(); // Reload the page to see changes
                        });
                    }
                });
            }
        });
    });
});
</script>

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
                        <!-- Search Section -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                            </div>
                        </div>

                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-4">
                                <label for="filter-course">Filter by Course</label>
                                <select id="filter-course" class="form-control">
                                    <option value="">All Courses</option>
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
                            <div class="col-md-6 col-lg-4">
                                <label for="filter-semester">Filter by Semester</label>
                                <select id="filter-semester" class="form-control">
                                    <option value="">All Semesters</option>
                                    <option value="1st">1st Semester</option>
                                    <option value="2nd">2nd Semester</option>
                                    <option value="Summer">Summer</option>
                                </select>
                            </div>
                        </div>

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
                                    while($row = $subject->fetch_assoc()): ?>
                                    <tr class="subject-row" data-course="<?php echo $row['course']; ?>" data-semester="<?php echo $row['semester']; ?>">
                                        <td class="text-center"><?php echo $i++; ?></td>
                                        <td>
                                            <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
                                            <p><small><b>Description:</b> <?php echo $row['description']; ?></small></p>
                                            <p><small><b>Total Units:</b> <?php echo $row['total_units']; ?></small></p>
                                            <p><small><b>Lec Units:</b> <?php echo $row['Lec_Units']; ?></small></p>
                                            <p><small><b>Lab Units:</b> <?php echo $row['Lab_Units']; ?></small></p>
                                            <p><small><b>Course:</b> <?php echo $row['course']; ?></small></p>
                                            <p><small><b>Year:</b> <?php echo $row['year']; ?></small></p>
                                            <p><small><b>Semester:</b> <?php echo $row['semester']; ?></small></p>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm edit_subject" data-id="<?php echo $row['id']; ?>" data-toggle="modal" data-target="#subjectModal">Edit</button>
                                            <button class="btn btn-danger btn-sm delete_subject" data-id="<?php echo $row['id']; ?>">Delete</button>
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
</div>

<!-- Modal for adding/editing subjects -->
<div class="modal fade" id="subjectModal" tabindex="-1" role="dialog" aria-labelledby="subjectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subjectModalLabel">Subject Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="manage-subject">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="Lec_Units">Lecture Units</label>
                        <input type="number" class="form-control" id="Lec_Units" name="Lec_Units" required>
                    </div>
                    <div class="form-group">
                        <label for="Lab_Units">Laboratory Units</label>
                        <input type="number" class="form-control" id="Lab_Units" name="Lab_Units" required>
                    </div>
                    <div class="form-group">
                        <label for="hours">Hours</label>
                        <input type="number" class="form-control" id="hours" name="hours" required>
                    </div>
                    <div class="form-group">
                        <label for="total_units">Total Units</label>
                        <input type="number" class="form-control" id="total_units" name="total_units" required>
                    </div>
                    <div class="form-group">
                        <label for="course">Course</label>
                        <input type="text" class="form-control" id="course" name="course" required>
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="text" class="form-control" id="year" name="year" required>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <input type="text" class="form-control" id="semester" name="semester" required>
                    </div>
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <input type="text" class="form-control" id="specialization" name="specialization">
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

<script>
$(document).ready(function() {
    // Edit subject
    $('.edit_subject').on('click', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'fetch_subject.php', // Create this PHP file to fetch subject data
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                $('#id').val(data.id);
                $('#subject').val(data.subject);
                $('#description').val(data.description);
                $('#Lec_Units').val(data.Lec_Units);
                $('#Lab_Units').val(data.Lab_Units);
                $('#hours').val(data.hours);
                $('#total_units').val(data.total_units);
                $('#course').val(data.course);
                $('#year').val(data.year);
                $('#semester').val(data.semester);
                $('#specialization').val(data.specialization);
            }
        });
    });
});
</script>

