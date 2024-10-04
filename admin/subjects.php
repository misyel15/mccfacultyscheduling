<?php
session_start(); // Start the session
include('db_connect.php');
include 'includes/header.php';

// Assuming the user department ID is stored in the session after login
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;
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
                                            <button class="btn btn-sm btn-primary edit_subject" type="button" data-id="<?php echo $row['id']; ?>" data-subject="<?php echo $row['subject']; ?>" data-description="<?php echo $row['description']; ?>" data-units="<?php echo $row['total_units']; ?>" data-lecunits="<?php echo $row['Lec_Units']; ?>" data-labunits="<?php echo $row['Lab_Units']; ?>" data-course="<?php echo $row['course']; ?>" data-year="<?php echo $row['year']; ?>" data-semester="<?php echo $row['semester']; ?>" data-special="<?php echo $row['specialization']; ?>" data-hours="<?php echo $row['hours']; ?>" data-toggle="modal" data-target="#subjectModal">
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
                                    <div class="col-sm-12">
                                        <label class="control-label">Subject</label>
                                        <input type="text" class="form-control" name="subject">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Description</label>
                                        <textarea class="form-control" name="description"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Total Units</label>
                                        <input type="number" class="form-control" name="total_units" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Lec Units</label>
                                        <input type="number" class="form-control" name="Lec_Units" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Lab Units</label>
                                        <input type="number" class="form-control" name="Lab_Units" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Hours</label>
                                        <input type="number" class="form-control" name="hours" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
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
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Year</label>
                                        <input type="text" class="form-control" name="year" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Semester</label>
                                        <input type="text" class="form-control" name="semester" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Specialization</label>
                                        <input type="text" class="form-control" name="specialization">
                                    </div>
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

<!-- Include script for handling form submission -->
<script>
$(document).ready(function() {
    $('#subjectTable').DataTable(); // Initialize DataTable

    // Handle form submission
    $('#manage-subject').submit(function(e) {
        e.preventDefault(); // Prevent default form submission
        $.ajax({
            url: 'ajax.php?action=save_subject', // PHP file to handle form submission
            method: 'POST',
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                if (response == 'success') {
                    Swal.fire('Success!', 'Subject has been saved.', 'success');
                    $('#subjectModal').modal('hide');
                    setTimeout(() => {
                        location.reload(); // Reload page to see changes
                    }, 1000);
                } else {
                    Swal.fire('Error!', response, 'error'); // Show error alert
                }
            }
        });
    });

    // Edit button click event
    $('.edit_subject').click(function() {
        const id = $(this).data('id');
        const subject = $(this).data('subject');
        const description = $(this).data('description');
        const totalUnits = $(this).data('units');
        const lecUnits = $(this).data('lecunits');
        const labUnits = $(this).data('labunits');
        const course = $(this).data('course');
        const year = $(this).data('year');
        const semester = $(this).data('semester');
        const specialization = $(this).data('special');
        const hours = $(this).data('hours');
        
        // Populate modal fields
        $('#subjectModal input[name="id"]').val(id);
        $('#subjectModal input[name="subject"]').val(subject);
        $('#subjectModal textarea[name="description"]').val(description);
        $('#subjectModal input[name="total_units"]').val(totalUnits);
        $('#subjectModal input[name="Lec_Units"]').val(lecUnits);
        $('#subjectModal input[name="Lab_Units"]').val(labUnits);
        $('#subjectModal input[name="hours"]').val(hours);
        $('#subjectModal select[name="course"]').val(course);
        $('#subjectModal input[name="year"]').val(year);
        $('#subjectModal input[name="semester"]').val(semester);
        $('#subjectModal input[name="specialization"]').val(specialization);

        $('#subjectModal').modal('show'); // Show the modal
    });

    // Additional Delete functionality can be added here
});

</script>

