<?php
session_start();
include('db_connect.php');
include 'includes/header.php';

// Function to sanitize input
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($input));
}

// CRUD Operations
if(isset($_POST['action'])) {
    $action = $_POST['action'];
    $response = ['status' => 'error', 'message' => 'Unknown error occurred'];
    
    switch($action) {
        case 'create':
        case 'update':
            $id = isset($_POST['id']) ? sanitize($_POST['id']) : '';
            $subject = sanitize($_POST['subject']);
            $description = sanitize($_POST['description']);
            $total_units = sanitize($_POST['total_units']);
            $lec_units = sanitize($_POST['Lec_Units']);
            $lab_units = sanitize($_POST['Lab_Units']);
            $hours = sanitize($_POST['hours']);
            $course = sanitize($_POST['course']);
            $year = sanitize($_POST['year']);
            $semester = sanitize($_POST['semester']);
            $specialization = sanitize($_POST['specialization']);
            $dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : 0;

            if($action == 'create') {
                $sql = "INSERT INTO subjects (subject, description, total_units, Lec_Units, Lab_Units, hours, course, year, semester, specialization, dept_id) 
                        VALUES ('$subject', '$description', '$total_units', '$lec_units', '$lab_units', '$hours', '$course', '$year', '$semester', '$specialization', '$dept_id')";
                $message = 'Subject has been created successfully.';
            } else {
                $sql = "UPDATE subjects SET 
                        subject = '$subject', 
                        description = '$description', 
                        total_units = '$total_units', 
                        Lec_Units = '$lec_units', 
                        Lab_Units = '$lab_units', 
                        hours = '$hours', 
                        course = '$course', 
                        year = '$year', 
                        semester = '$semester', 
                        specialization = '$specialization' 
                        WHERE id = '$id'";
                $message = 'Subject has been updated successfully.';
            }

            if($conn->query($sql)){
                $response = ['status' => 'success', 'message' => $message];
            } else {
                $response = ['status' => 'error', 'message' => "Error: " . $conn->error];
            }
            break;

        case 'delete':
            $id = sanitize($_POST['id']);
            $sql = "DELETE FROM subjects WHERE id = '$id'";
            if($conn->query($sql)){
                $response = ['status' => 'success', 'message' => 'Subject has been deleted successfully.'];
            } else {
                $response = ['status' => 'error', 'message' => "Error: " . $conn->error];
            }
            break;

        case 'read':
            $id = sanitize($_POST['id']);
            $sql = "SELECT * FROM subjects WHERE id = '$id'";
            $result = $conn->query($sql);
            if($result->num_rows > 0){
                $response = ['status' => 'success', 'data' => $result->fetch_assoc()];
            } else {
                $response = ['status' => 'error', 'message' => 'No data found'];
            }
            break;
    }
    
    echo json_encode($response);
    exit;
}

// ... rest of your PHP code ...
?>
<!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Include DataTables CSS -->
<link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Include DataTables JS -->
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
                                    while($row = $subject->fetch_assoc()):
                                    ?>
                                    <tr class="subject-row" data-course="<?php echo $row['course']; ?>" data-semester="<?php echo $row['semester']; ?>">
                                        <td class="text-center"><?php echo $i++; ?></td>
                                        <td>
                                            <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
                                            <p><small><b>Description:</b> <?php echo $row['description']; ?></small></p>
                                            <p><small><b>Total Units:</b> <?php echo $row['total_units']; ?></small></p>
                                            <p><small><b>Lec Units:</b> <?php echo $row['Lec_Units']; ?></small></p>
                                            <p><small><b>Lab Units:</b> <?php echo $row['Lab_Units']; ?></small></p>
                                            <p><small><b>Hours:</b> <?php echo $row['hours']; ?></small></p>
                                            <p><small><b>Course:</b> <?php echo $row['course']; ?></small></p>
                                            <p><small><b>Year:</b> <?php echo $row['year']; ?></small></p>
                                            <p><small><b>Semester:</b> <?php echo $row['semester']; ?></small></p>
                                            <p><small><b>Specialization:</b> <?php echo $row['specialization']; ?></small></p>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary edit_subject" type="button" data-id="<?php echo $row['id']; ?>">
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
                        <form action="" id="manage-subject">
                            <div class="modal-body">
                                <input type="hidden" name="id">
                                <div class="form-group">
                                    <label class="control-label">Subject</label>
                                    <input type="text" class="form-control" name="subject">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea class="form-control" name="description"></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Total Units</label>
                                    <input type="number" class="form-control" name="total_units" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Lec Units</label>
                                    <input type="number" class="form-control" name="Lec_Units" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Lab Units</label>
                                    <input type="number" class="form-control" name="Lab_Units" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Hours</label>
                                    <input type="number" class="form-control" name="hours" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Course</label>
                                    <select class="form-control" name="course" required>
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
                                    <input type="text" class="form-control" name="year" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Semester</label>
                                    <input type="text" class="form-control" name="semester" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Specialization</label>
                                    <input type="text" class="form-control" name="specialization">
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
    var table = $('#subjectTable').DataTable();

    // Function to reset modal form
    function resetForm() {
        $('#manage-subject')[0].reset();
        $('input[name="id"]').val('');
    }

    // Handle form submission (Create and Update)
    $('#manage-subject').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var action = $('input[name="id"]').val() ? 'update' : 'create';
        
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: formData + '&action=' + action,
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                    }).then((result) => {
                        $('#subjectModal').modal('hide');
                        table.ajax.reload();
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error!', 'An error occurred while processing your request.', 'error');
            }
        });
    });

    // Edit button click event (Read)
    $(document).on('click', '.edit_subject', function() {
        var id = $(this).data('id');
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: { action: 'read', id: id },
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    var subject = data.data;
                    $('#subjectModal input[name="id"]').val(subject.id);
                    $('#subjectModal input[name="subject"]').val(subject.subject);
                    $('#subjectModal textarea[name="description"]').val(subject.description);
                    $('#subjectModal input[name="total_units"]').val(subject.total_units);
                    $('#subjectModal input[name="Lec_Units"]').val(subject.Lec_Units);
                    $('#subjectModal input[name="Lab_Units"]').val(subject.Lab_Units);
                    $('#subjectModal input[name="hours"]').val(subject.hours);
                    $('#subjectModal select[name="course"]').val(subject.course);
                    $('#subjectModal input[name="year"]').val(subject.year);
                    $('#subjectModal input[name="semester"]').val(subject.semester);
                    $('#subjectModal input[name="specialization"]').val(subject.specialization);

                    $('#subjectModal').modal('show');
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to fetch subject data.', 'error');
            }
        });
    });

    // Delete button click event
    $(document).on('click', '.delete_subject', function() {
        var id = $(this).data('id');
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
                    url: window.location.href,
                    method: 'POST',
                    data: { action: 'delete', id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire('Deleted!', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'An error occurred while deleting the subject.', 'error');
                    }
                });
            }
        });
    });

    // Reset form when modal is closed
    $('#subjectModal').on('hidden.bs.modal', function () {
        resetForm();
    });

    // New Entry button click event
    $('.btn-new-entry').click(function() {
        resetForm();
        $('#subjectModal').modal('show');
    });

    // Filter functionality
    $('#filter-course, #filter-semester').change(function() {
        table.draw();
    });

    // Custom filtering function
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var course = $('#filter-course').val();
            var semester = $('#filter-semester').val();
            var rowCourse = data[1]; // Assuming course is in the second column
            var rowSemester = data[2]; // Assuming semester is in the third column

            if ((course === "" || rowCourse.includes(course)) &&
                (semester === "" || rowSemester.includes(semester)))
            {
                return true;
            }
            return false;
        }
    );
});
</script>