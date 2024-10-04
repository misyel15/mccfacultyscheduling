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
        } elseif ($action === 'fetch_courses') {
            // Fetch all courses for the department
            $course_query = $conn->query("SELECT * FROM courses WHERE dept_id = '$dept_id' ORDER BY id ASC");
            $courses = [];
            while ($row = $course_query->fetch_assoc()) {
                $courses[] = $row;
            }
            echo json_encode($courses); // Return courses as JSON
            exit; // Exit after sending data
        }
        exit; // Exit after handling the request
    }
}
?>

<!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">

<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

<div class="container-fluid" style="margin-top:100px;">
    <div class="col-lg-14">
        <div class="row">
            <!-- Table Panel -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <b>Course List</b>
                        <span class="">
                            <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#courseModal"><i class="fa fa-user-plus"></i> New Entry</button>
                            <button class="btn btn-secondary btn-sm float-right mr-2" id="refreshCourses"><i class="fa fa-sync"></i> Refresh</button> <!-- Refresh button -->
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
                                <tbody id="courseBody"> <!-- Added an ID to tbody for dynamic content -->
                                    <?php 
                                    $i = 1;
                                    $course = $conn->query("SELECT * FROM courses WHERE dept_id = '$dept_id' ORDER BY id ASC");
                                    while($row = $course->fetch_assoc()):
                                    ?>
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
    function _reset() {
        $('#manage-course').get(0).reset();
        $('#manage-course input, #manage-course textarea').val('');
        $("input[name='action']").val('save_course'); // Reset action to default
    }

    function loadCourses() {
        $.ajax({
            url: '',
            method: 'POST',
            data: { action: 'fetch_courses' },
            success: function(response) {
                const courses = JSON.parse(response);
                const tbody = $('#courseBody');
                tbody.empty(); // Clear current rows
                let i = 1;
                courses.forEach(course => {
                    tbody.append(`
                        <tr>
                            <td class="text-center">${i++}</td>
                            <td class="">
                                <p>Course: <b>${course.course}</b></p>
                                <p>Description: <small><b>${course.description}</b></small></p>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary edit_course" type="button" data-id="${course.id}" data-course="${course.course}" data-description="${course.description}" data-toggle="modal" data-target="#courseModal">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete_course" type="button" data-id="${course.id}">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </td>
                        </tr>
                    `);
                });
            }
        });
    }

    $(document).ready(function() {
        $('#course-table').DataTable(); // Initialize DataTable

        // Refresh course list on button click
        $('#refreshCourses').click(function() {
            loadCourses();
        });

        // Handle form submission for adding/editing courses
        $('#manage-course').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response == 0) {
                        Swal.fire('Error!', 'Course already exists.', 'error');
                    } else if (response == 1) {
                        Swal.fire('Success!', 'Course successfully added.', 'success');
                        loadCourses(); // Refresh course list
                        _reset(); // Reset the form
                    } else if (response == 2) {
                        Swal.fire('Success!', 'Course successfully updated.', 'success');
                        loadCourses(); // Refresh course list
                        _reset(); // Reset the form
                    }
                }
            });
        });

        // Edit course
        $('.edit_course').click(function() {
            const id = $(this).data('id');
            const course = $(this).data('course');
            const description = $(this).data('description');

            $('input[name="id"]').val(id);
            $('input[name="course"]').val(course);
            $('textarea[name="description"]').val(description);
            $("input[name='action']").val('edit_course'); // Change action to edit
        });

        // Delete course
        $('.delete_course').click(function() {
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
                        data: { action: 'delete_course', id: id },
                        success: function(response) {
                            Swal.fire('Deleted!', 'Course has been deleted.', 'success');
                            loadCourses(); // Refresh course list
                        }
                    });
                }
            });
        });

        // Load courses initially
        loadCourses();
    });
</script>
