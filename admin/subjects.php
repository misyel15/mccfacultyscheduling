<?php
session_start(); // Start the session
include('db_connect.php');
include 'includes/header.php';

// Assuming the user department ID is stored in the session after login
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;

// Handle Add/Edit/Delete in the same page
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handling Add/Edit form submission
    if (isset($_POST['action']) && $_POST['action'] == 'manage') {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $subject = $_POST['subject'];
        $description = $_POST['description'];
        $total_units = $_POST['total_units'];
        $lec_units = $_POST['Lec_Units'];
        $lab_units = $_POST['Lab_Units'];
        $hours = $_POST['hours'];
        $course = $_POST['course'];
        $year = $_POST['year'];
        $semester = $_POST['semester'];
        $specialization = $_POST['specialization'];

        if ($id) {
            // Update subject
            $sql = "UPDATE subjects SET subject='$subject', description='$description', total_units='$total_units', Lec_Units='$lec_units', Lab_Units='$lab_units', hours='$hours', course='$course', year='$year', semester='$semester', specialization='$specialization' WHERE id='$id'";
        } else {
            // Add new subject
            $sql = "INSERT INTO subjects (subject, description, total_units, Lec_Units, Lab_Units, hours, course, year, semester, specialization) 
                    VALUES ('$subject', '$description', '$total_units', '$lec_units', '$lab_units', '$hours', '$course', '$year', '$semester', '$specialization')";
        }

        if ($conn->query($sql)) {
            echo 'success';
        } else {
            echo 'Error: ' . $conn->error;
        }
        exit;
    }

    // Handling Delete request
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $sql = "DELETE FROM subjects WHERE id = '$id'";
        if ($conn->query($sql)) {
            echo 'success';
        } else {
            echo 'Error: ' . $conn->error;
        }
        exit;
    }
}
?>

<!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Include DataTables CSS -->
<link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                                    $sql = $dept_id ? "SELECT * FROM subjects WHERE dept_id = '$dept_id' ORDER BY id ASC" : "SELECT * FROM subjects ORDER BY id ASC";
                                    $subject = $conn->query($sql);
                                    while($row = $subject->fetch_assoc()):
                                    ?>
                                    <tr class="subject-row">
                                        <td class="text-center"><?php echo $i++; ?></td>
                                        <td>
                                            <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
                                            <!-- Other details -->
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary edit_subject" type="button" data-id="<?php echo $row['id']; ?>" data-subject="<?php echo $row['subject']; ?>" data-toggle="modal" data-target="#subjectModal">
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

            <!-- Modal -->
            <div class="modal fade" id="subjectModal" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Subject Form</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form id="manage-subject">
                            <input type="hidden" name="action" value="manage">
                            <input type="hidden" name="id">
                            <!-- Form fields (Subject, Description, etc.) -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#subjectTable').DataTable();

    // Handle Add/Edit form submission
    $('#manage-subject').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '', // Same page handles the request
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response == 'success') {
                    Swal.fire('Success!', 'Subject saved successfully.', 'success');
                    $('#subjectModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    Swal.fire('Error!', response, 'error');
                }
            }
        });
    });

    // Edit button click event
    $('.edit_subject').click(function() {
        var id = $(this).data('id');
        // Populate modal fields using data-* attributes
        $('#subjectModal input[name="id"]').val(id);
        // Fetch other data and populate modal fields
        $('#subjectModal').modal('show');
    });

    // Delete button click event
    $('.delete_subject').click(function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You can't undo this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '', // Same page handles delete request
                    method: 'POST',
                    data: {id: id, action: 'delete'},
                    success: function(response) {
                        if (response == 'success') {
                            Swal.fire('Deleted!', 'Subject deleted successfully.', 'success');
                            setTimeout(() => location.reload(), 1000);
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
