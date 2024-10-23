
<?php
session_start();
include('db_connect.php');
include 'includes/header.php';

// Assuming you store the department ID in the session during login
// Example: $_SESSION['dept_id'] = $user['dept_id'];
$dept_id = $_SESSION['dept_id']; // Get the department ID from the session
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
                                    $subject = $conn->query("SELECT * FROM subjects WHERE dept_id = '$dept_id' ORDER BY id ASC");
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
                            <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>"> <!-- Hidden dept_id input -->
    
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Subject</label>
                                        <input type="text" class="form-control" name="subject">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Description</label>
                                        <textarea class="form-control" cols="30" rows='3' name="description"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Total Units</label>
                                        <input type="text" class="form-control" name="units">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Lec Units</label>
                                        <input type="text" class="form-control" name="lec_units">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Lab Units</label>
                                        <input type="text" class="form-control" name="lab_units">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Hours</label>
                                        <input type="text" class="form-control" name="hours">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label for="course" class="control-label">Course</label>
                                        <select class="form-control" name="course" id="course" required>
                                            <option value="0" disabled selected>Select Course</option>
                                            <?php 
                                                $sql = "SELECT * FROM courses";
                                                $query = $conn->query($sql);
                                                while($row= $query->fetch_array()):
                                                    $course = $row['course'];
                                                ?>
                                            <option value="<?php echo  $course ?>"><?php echo ucwords($course) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label for="cyear" class="control-label">Year</label>
                                        <select class="form-control" name="cyear" id="cyear">
                                            <option value="" disabled selected>Select Year</option>
                                            <option value="1st">1st</option>
                                            <option value="2nd">2nd</option>
                                            <option value="3rd">3rd</option>
                                            <option value="4th">4th</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label for="semester" class="control-label">Semester</label>
                                        <select class="form-control" name="semester" id="semester">
                                            <option value="" disabled selected>Select Semester</option>
                                            <option value="1st">1st</option>
                                            <option value="2nd">2nd</option>
                                            <option value="3nd">Summer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label for="specialization" class="control-label">Specialization</label>
                                        <select class="form-control" name="specialization" id="specialization">
                                            <option value="" disabled selected>Select Specialization</option>
                                            <option value="Major">Major</option>
                                            <option value="Minor">Minor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">              
                                   <button type="button" class="btn btn-primary" id="saveSectionBtn">Submit</button>
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

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#subjectTable').DataTable();

    // Add new subject
    $('#manage-subject').submit(function(e) {
        e.preventDefault();
        
        // Basic input validation
        var subject = $('[name="subject"]').val().trim();
        var description = $('[name="description"]').val().trim();
        var units = $('[name="units"]').val().trim();
        var lec_units = $('[name="lec_units"]').val().trim();
        var lab_units = $('[name="lab_units"]').val().trim();
        var hours = $('[name="hours"]').val().trim();
        var course = $('[name="course"]').val();
        var cyear = $('[name="cyear"]').val();
        var semester = $('[name="semester"]').val();
        var specialization = $('[name="specialization"]').val();

        if (!subject || !course || !cyear || !semester) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields!',
            });
            return;
        }

        $.ajax({
            url: 'ajax.php?action=save_subject',
            data: new FormData($(this)[0]),
            method: 'POST',
            processData: false,
            contentType: false,
            success: function(resp) {
                if (resp == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Subject successfully saved!',
                        showConfirmButton: true,
                        
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error saving subject!',
                        text: resp // Display the server response in case of error
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong with the AJAX request!',
                });
            }
        });
    });

    // Edit subject
    $('.edit_subject').click(function() {
        var form = $('#manage-subject');
        form.find("[name='id']").val($(this).data('id'));
        form.find("[name='subject']").val($(this).data('subject'));
        form.find("[name='description']").val($(this).data('description'));
        form.find("[name='units']").val($(this).data('units'));
        form.find("[name='lec_units']").val($(this).data('lecunits'));
        form.find("[name='lab_units']").val($(this).data('labunits'));
        form.find("[name='course']").val($(this).data('course'));
        form.find("[name='cyear']").val($(this).data('year'));
        form.find("[name='semester']").val($(this).data('semester'));
        form.find("[name='specialization']").val($(this).data('special'));
        form.find("[name='hours']").val($(this).data('hours'));
    });

    // Delete subject
    $('.delete_subject').click(function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure you want to delete this subject?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax.php?action=delete_subject',
                    method: 'POST',
                    data: { id: id },
                    success: function(resp) {
                        if (resp == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Subject successfully deleted!',
                                showConfirmButton: true,
                             
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    location.reload();
                                }
                            });
                        }
                    }
                });
            }
        });
    });

    // Filter functionality
    $('#filter-course, #filter-semester').change(function() {
        var courseFilter = $('#filter-course').val().toLowerCase();
        var semesterFilter = $('#filter-semester').val().toLowerCase();

        $('.subject-row').each(function() {
            var course = $(this).data('course').toLowerCase();
            var semester = $(this).data('semester').toLowerCase();
            var show = true;

            if (courseFilter && course !== courseFilter) {
                show = false;
            }

            if (semesterFilter && semester !== semesterFilter) {
                show = false;
            }

            $(this).toggle(show);
        });
    });
});

</script>
