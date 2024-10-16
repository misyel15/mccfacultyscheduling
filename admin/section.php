<?php
session_start();
include('db_connect.php'); // Database connection
include 'includes/header.php';

// Assuming you store department ID in the session during login
$dept_id = $_SESSION['dept_id'];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<div class="container-fluid" style="margin-top:100px;">
    <div class="col-lg-14">
        <div class="row">
            <div class="modal fade" id="sectionModal" tabindex="-1" role="dialog" aria-labelledby="sectionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Section Form</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="manage-section">
                                <input type="hidden" name="id">
                                <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="course">Course</label>
                                        <select class="form-control" name="course" id="course" required>
                                            <option value="" disabled selected>Select Course</option>
                                            <?php
                                            $sql = "SELECT * FROM courses";
                                            $query = $conn->query($sql);
                                            while($prow = $query->fetch_assoc()){
                                                echo "<option value='".$prow['course']."'>".$prow['course']."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="cyear">Year</label>
                                        <select class="form-control" name="cyear" id="cyear" required>
                                            <option value="" disabled selected>Select Year</option>
                                            <option value="1st">1st</option>
                                            <option value="2nd">2nd</option>
                                            <option value="3rd">3rd</option>
                                            <option value="4th">4th</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="section">Section</label>
                                    <input type="text" class="form-control" name="section" id="section" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="saveSectionBtn">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <b>Section List</b>
                        <button class="btn btn-primary float-right" data-toggle="modal" data-target="#sectionModal">
                            <i class="fa fa-plus"></i> New Entry
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover" id="sectionTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $sections = $conn->query("SELECT * FROM section WHERE dept_id = '$dept_id' ORDER BY id ASC");
                                while($row = $sections->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td>
                                        <p>Course: <b><?php echo $row['course'] ?></b></p>
                                        <p>Year: <b><?php echo $row['year'] ?></b></p>
                                        <p>Section: <b><?php echo $row['section'] ?></b></p>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary edit_section" data-id="<?php echo $row['id'] ?>" 
                                                data-course="<?php echo $row['course'] ?>" 
                                                data-cyear="<?php echo $row['year'] ?>" 
                                                data-section="<?php echo $row['section'] ?>">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger delete_section" data-id="<?php echo $row['id'] ?>">
                                            Delete
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

<script>
    $('#saveSectionBtn').click(function() {
        $('#manage-section').submit();
    });

    $('#manage-section').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax.php?action=save_section',
            data: new FormData(this),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    Swal.fire('Success', 'Section added successfully!', 'success').then(() => location.reload());
                } else if (resp == 3) {
                    Swal.fire('Error', 'Section already exists!', 'error');
                }
            }
        });
    });

    $('.delete_section').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Confirm Deletion',
            icon: 'warning',
            showCancelButton: true
        }).then(result => {
            if (result.isConfirmed) {
                $.post('ajax.php?action=delete_section', { id: id }, function(resp) {
                    if (resp == 1) location.reload();
                });
            }
        });
    });

    $(document).ready(function() {
        $('#sectionTable').DataTable();
    });
</script>
