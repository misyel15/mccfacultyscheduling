<?php
session_start(); // Start the session
include('db_connect.php');
include 'includes/header.php';

// Assuming the user department ID is stored in the session after login
$dept_id = $_SESSION['dept_id'] ?? null; // Use null coalescing operator for cleaner code

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'save_subject') {
        // Save or Update Subject
        $id = $_POST['id'] ?? null;
        $subject = $_POST['subject'] ?? '';
        $description = $_POST['description'] ?? '';
        $total_units = $_POST['total_units'] ?? 0;
        $Lec_Units = $_POST['Lec_Units'] ?? 0;
        $Lab_Units = $_POST['Lab_Units'] ?? 0;
        $course = $_POST['course'] ?? '';
        $year = $_POST['year'] ?? '';
        $semester = $_POST['semester'] ?? '';
        $specialization = $_POST['specialization'] ?? '';
        $hours = $_POST['hours'] ?? 0;

        // Prepare statement to prevent SQL injection
        if (empty($id)) {
            // Insert new subject
            $sql = $conn->prepare("INSERT INTO subjects (subject, description, total_units, Lec_Units, Lab_Units, course, year, semester, specialization, hours, dept_id) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $sql->bind_param("ssiiisssssi", $subject, $description, $total_units, $Lec_Units, $Lab_Units, $course, $year, $semester, $specialization, $hours, $_SESSION['dept_id']);
        } else {
            // Update existing subject
            $sql = $conn->prepare("UPDATE subjects SET subject = ?, description = ?, total_units = ?, Lec_Units = ?, Lab_Units = ?, course = ?, year = ?, semester = ?, specialization = ?, hours = ? WHERE id = ?");
            $sql->bind_param("ssiiissssii", $subject, $description, $total_units, $Lec_Units, $Lab_Units, $course, $year, $semester, $specialization, $hours, $id);
        }

        if ($sql->execute()) {
            echo 'success';
        } else {
            echo 'Error: ' . $sql->error;
        }
    } elseif ($action == 'delete_subject') {
        // Delete subject
        $id = $_POST['id'] ?? null;
        if ($id) {
            $sql = $conn->prepare("DELETE FROM subjects WHERE id = ?");
            $sql->bind_param("i", $id);
            if ($sql->execute()) {
                echo 'success';
            } else {
                echo 'Error: ' . $sql->error;
            }
        }
    }
    exit; // Always exit after processing AJAX requests
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
                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-4">
                                <label for="filter-course">Filter by Course</label>
                                <select id="filter-course" class="form-control">
                                    <option value="">All Courses</option>
                                    <?php 
                                        $sql = "SELECT * FROM courses";
                                        $query = $conn->query($sql);
                                        while ($row = $query->fetch_array()):
                                    ?>
                                    <option value="<?php echo $row['course']; ?>"><?php echo ucwords($row['course']); ?></option>
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
                                    $subject = $dept_id 
                                        ? $conn->query("SELECT * FROM subjects WHERE dept_id = '$dept_id' ORDER BY id ASC")
                                        : $conn->query("SELECT * FROM subjects ORDER BY id ASC");

                                    while ($row = $subject->fetch_assoc()):
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
                                            <button class="btn btn-sm btn-primary edit_subject" type="button" data-id="<?php echo $row['id']; ?>" data-subject="<?php echo $row['subject']; ?>" data-description="<?php echo $row['description']; ?>" data-units="<?php echo $row['total_units']; ?>" data-lecunits="<?php echo $row['Lec_Units']; ?>" data-labunits="<?php echo $row['Lab_Units']; ?>" data-course="<?php echo $row['course']; ?>" data-year="<?php echo $row['year']; ?>" data-semester="<?php echo $row['semester']; ?>" data-special="<?php echo $row['specialization']; ?>" data-hours="<?php echo $row['hours']; ?>" data-toggle="modal" data-target="#subjectModal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete_subject" type="button" data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-trash"></i> Delete
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
        </div>
    </div>
</div>

<!-- Subject Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1" role="dialog" aria-labelledby="subjectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subjectModalLabel">Subject Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="subjectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="subject-id">
                    <div class="form-group">
                        <label for="subject">Subject Name</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="total_units">Total Units</label>
                        <input type="number" class="form-control" id="total_units" name="total_units" required>
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
                        <label for="course">Course</label>
                        <input type="text" class="form-control" id="course" name="course" required>
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="text" class="form-control" id="year" name="year" required>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select class="form-control" id="semester" name="semester" required>
                            <option value="1st">1st Semester</option>
                            <option value="2nd">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <input type="text" class="form-control" id="specialization" name="specialization">
                    </div>
                    <div class="form-group">
                        <label for="hours">Hours</label>
                        <input type="number" class="form-control" id="hours" name="hours" required>
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
    // Initialize DataTables
    $('#subjectTable').DataTable();

    // Save subject
    $('#subjectForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize() + '&action=save_subject';
        
        $.ajax({
            type: 'POST',
            url: 'subject_management.php',
            data: formData,
            success: function(response) {
                if (response.trim() == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Subject saved successfully!',
                    }).then(() => {
                        location.reload(); // Reload page to see changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                });
            }
        });
    });

    // Edit subject button click
    $('.edit_subject').click(function() {
        var id = $(this).data('id');
        $('#subject-id').val(id);
        $('#subject').val($(this).data('subject'));
        $('#description').val($(this).data('description'));
        $('#total_units').val($(this).data('units'));
        $('#Lec_Units').val($(this).data('lecunits'));
        $('#Lab_Units').val($(this).data('labunits'));
        $('#course').val($(this).data('course'));
        $('#year').val($(this).data('year'));
        $('#semester').val($(this).data('semester'));
        $('#specialization').val($(this).data('special'));
        $('#hours').val($(this).data('hours'));
    });

    // Delete subject
    $('.delete_subject').click(function() {
        var id = $(this).data('id');
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
                    type: 'POST',
                    url: 'subject_management.php',
                    data: { action: 'delete_subject', id: id },
                    success: function(response) {
                        if (response.trim() == 'success') {
                            Swal.fire('Deleted!', 'Your subject has been deleted.', 'success').then(() => {
                                location.reload(); // Reload to update the list
                            });
                        } else {
                            Swal.fire('Error!', response, 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>

