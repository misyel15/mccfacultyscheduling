<?php
session_start(); // Start the session
include('db_connect.php');
include 'includes/header.php';

// Assuming the user department ID is stored in the session after login
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;
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

<div class="container-fluid" style="margin-left: 1%;">
    <style>
        input[type=checkbox] {
            transform: scale(1.5);
            padding: 10px;
        }
        .container {
            height: 1000px;
            overflow-y: auto;
        }
        td {
            vertical-align: middle !important;
        }
        td p {
            margin: unset;
        }
        img {
            max-width: 100px;
            max-height: 150px;
        }
        @media (max-width: 768px) {
            .table td, .table th {
                white-space: nowrap;
            }
            .table-responsive {
                margin-bottom: 15px;
                overflow-x: auto;
            }
        }
    </style>

    <div class="col-lg-14" style="margin-top:100px; margin-left:-1%;">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <b>Faculty List</b>
                        <button class="btn btn-primary btn-sm" type="button" id="new_faculty">
                            <i class="fa fa-user-plus"></i> New Entry
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed table-hover" id="facultyTable">
                                <colgroup>
                                    <col width="5%">
                                    <col width="20%">
                                    <col width="30%">
                                    <col width="20%">
                                    <col width="10%">
                                    <col width="15%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>ID No</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    $faculty = $conn->query("SELECT *, concat(lastname, ', ', firstname, ' ', middlename) as name FROM faculty WHERE dept_id = '$dept_id' ORDER BY name ASC");
                                    while($row=$faculty->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++ ?></td>
                                        <td><p><b><?php echo $row['id_no'] ?></b></p></td>
                                        <td><p><b><?php echo ucwords($row['name']) ?></b></p></td>
                                        <td><p><b><?php echo $row['email'] ?></b></p></td>
                                        <td class="text-right"><p><b><?php echo $row['contact'] ?></b></p></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info view_faculty" type="button" data-id="<?php echo $row['id'] ?>">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary edit_faculty" type="button" data-id="<?php echo $row['id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete_faculty" type="button" data-id="<?php echo $row['id'] ?>">
                                                <i class="fa fa-trash-alt"></i>
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

<!-- Modal -->
<div class="modal fade" id="uni_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="manage-section">
                    <input type="hidden" name="id">
                    <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>">
                    <div class="form-group">
                        <label for="name">Faculty Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#facultyTable').DataTable();

        $('#new_faculty').click(function(){
            uni_modal("New Entry", "manage_faculty.php", 'mid-large');
        });

        $('.view_faculty').click(function(){
            const id = $(this).data('id');
            uni_modal("Faculty Details", "view_faculty.php?id=" + id, '');
        });

        $('.edit_faculty').click(function(){
            const id = $(this).data('id');
            uni_modal("Manage Faculty", "manage_faculty.php?id=" + id, 'mid-large');
        });

        $('.delete_faculty').click(function(){
            const id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this data!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_faculty(id);
                }
            });
        });

        function delete_faculty(id){
            $.ajax({
                url: 'ajax.php?action=delete_faculty',
                method: 'POST',
                data: { id: id },
                success: function(response){
                    if(response == 1){
                        Swal.fire('Deleted!', 'Faculty data successfully deleted.', 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Oops...', 'Something went wrong!', 'error');
                    }
                },
                error: function(){
                    Swal.fire('Oops...', 'Request failed!', 'error');
                }
            });
        }

        function uni_modal(title, url, size = ''){
            $.ajax({
                url: url,
                success: function(resp){
                    if(resp){
                        $('#uni_modal .modal-title').html(title);
                        $('#uni_modal .modal-body').html(resp);
                        $('#uni_modal .modal-dialog').attr('class', 'modal-dialog ' + size);
                        $('#uni_modal').modal('show');
                    }
                }
            });
        }
    });
</script>
