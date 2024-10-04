<?php
session_start();
include('db_connect.php');
include 'includes/header.php';

// Assuming you store the department ID in the session during login
$dept_id = $_SESSION['dept_id']; // Get the department ID from the session

// Handle form submissions for saving, updating, and deleting courses
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'save_course') {
            // Save new course
            $course = $_POST['course'];
            $description = $_POST['description'];

            // Check if the course already exists
            $check_query = $conn->query("SELECT * FROM courses WHERE course = '$course' AND dept_id = '$dept_id'");
            if ($check_query->num_rows > 0) {
                echo 0; // Course already exists
            } else {
                $conn->query("INSERT INTO courses (course, description, dept_id) VALUES ('$course', '$description', '$dept_id')");
                echo 1; // Course successfully added
            }
        } elseif ($action === 'edit_course') {
            // Update existing course
            $id = $_POST['id'];
            $course = $_POST['course'];
            $description = $_POST['description'];

            $conn->query("UPDATE courses SET course = '$course', description = '$description' WHERE id = '$id'");
            echo 2; // Course successfully updated
        } elseif ($action === 'delete_course') {
            // Delete course
            $id = $_POST['id'];
            $conn->query("DELETE FROM courses WHERE id = '$id'");
            echo 1; // Course successfully deleted
        }
        exit; // Exit after handling the request
    }
}
?>
<!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Include Bootstrap CSS and SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Include DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<div class="container-fluid" style="margin-top:100px;">
    <div class="col-lg-14">
        <div class="row">
            <!-- Table Panel -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <b>Course List</b>
                        <span class="">
                            <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#courseModal">
                                <i class="fa fa-user-plus"></i> New Entry
                            </button>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="course-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Course</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    $course = $conn->query("SELECT * FROM courses WHERE dept_id = '$dept_id' ORDER BY id ASC");
                                    while($row = $course->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i++ ?></td>
                                            <td class="">
                                                <p>Course: <b><?php echo $row['course'] ?></b></p>
                                                <p>Description: <small><b><?php echo $row['description'] ?></b></small></p>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-primary edit_course" type="button" data-id="<?php echo $row['id'] ?>" data-course="<?php echo $row['course'] ?>" data-description="<?php echo $row['description'] ?>" data-toggle="modal" data-target="#courseModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger delete_course" type="button" data-id="<?php echo $row['id'] ?>">
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
            <div class="modal fade" id="courseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="courseModalLabel">Course Form</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" method="POST" id="manage-course">
                            <div class="modal-body">
                                <input type="hidden" name="id">
                                <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>">
                                <input type="hidden" name="action" value="save_course"> <!-- Default action -->
                                <div class="form-group">
                                    <label class="control-label">Course</label>
                                    <input type="text" class="form-control" name="course" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea class="form-control" cols="30" rows='3' name="description" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal -->
        </div>
    </div>
</div>

<style>
    td {
        vertical-align: middle !important;
    }
</style>

<script>
   // Function to reset the form
function _reset() {
    $('#manage-course').get(0).reset();
    $('#manage-course input, #manage-course textarea').val('');
    $("input[name='action']").val('save_course');
}

// Edit course button click event
$('.edit_course').click(function() {
    _reset();
    var cat = $('#manage-course');
    cat.find("[name='id']").val($(this).attr('data-id'));
    cat.find("[name='course']").val($(this).attr('data-course'));
    cat.find("[name='description']").val($(this).attr('data-description'));
    $("input[name='action']").val('edit_course');
});

// Delete course button click event
$('.delete_course').click(function() {
    var id = $(this).attr('data-id');
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
            delete_course(id);
        }
    });
});

// Function to save or update a course
function save_course() {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to save these changes?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!'
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = $('#manage-course').serialize();
            
            $.ajax({
                url: '',
                method: 'POST',
                data: formData,
                success: function(resp) {
                    if (resp == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Course successfully added.',
                            showConfirmButton: true
                        }).then(function() {
                            location.reload();
                        });
                    } else if (resp == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Course already exists.',
                            showConfirmButton: true
                        });
                    } else if (resp == 2) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'Course successfully updated.',
                            showConfirmButton: true
                        }).then(function() {
                            location.reload();
                        });
                    }
                }
            });
        }
    });
}

// Form submission event
$('#manage-course').on('submit', function(e) {
    e.preventDefault();
    save_course();
});

// Function to delete a course
function delete_course(id) {
    $.ajax({
        url: '',
        method: 'POST',
        data: { action: 'delete_course', id: id },
        success: function(resp) {
            if (resp == 1) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Data successfully deleted.',
                    showConfirmButton: true
                }).then(function() {
                    location.reload();
                });
            }
        }
    });
}

// Initialize DataTable
$(document).ready(function() {
    $('#course-table').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true
    });
});
</script>
