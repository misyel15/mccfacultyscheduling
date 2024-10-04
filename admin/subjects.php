<?php
session_start();
include('db_connect.php');
include 'includes/header.php';

// Assuming the user department ID is stored in the session after login
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : null;

// Function to handle subject operations
function handleSubjectOperation($conn, $dept_id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $operation = $_POST['operation'] ?? '';
        $id = $_POST['id'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $description = $_POST['description'] ?? '';
        $total_units = $_POST['total_units'] ?? '';
        $lec_units = $_POST['Lec_Units'] ?? '';
        $lab_units = $_POST['Lab_Units'] ?? '';
        $hours = $_POST['hours'] ?? '';
        $course = $_POST['course'] ?? '';
        $year = $_POST['year'] ?? '';
        $semester = $_POST['semester'] ?? '';
        $specialization = $_POST['specialization'] ?? '';

        switch ($operation) {
            case 'save':
                if (empty($id)) {
                    // Insert new subject
                    $stmt = $conn->prepare("INSERT INTO subjects (subject, description, total_units, Lec_Units, Lab_Units, hours, course, year, semester, specialization, dept_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssiiiissssi", $subject, $description, $total_units, $lec_units, $lab_units, $hours, $course, $year, $semester, $specialization, $dept_id);
                } else {
                    // Update existing subject
                    $stmt = $conn->prepare("UPDATE subjects SET subject = ?, description = ?, total_units = ?, Lec_Units = ?, Lab_Units = ?, hours = ?, course = ?, year = ?, semester = ?, specialization = ? WHERE id = ? AND dept_id = ?");
                    $stmt->bind_param("ssiiiissssii", $subject, $description, $total_units, $lec_units, $lab_units, $hours, $course, $year, $semester, $specialization, $id, $dept_id);
                }
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Subject " . (empty($id) ? "added" : "updated") . " successfully.";
                } else {
                    $_SESSION['error'] = "Error: " . $stmt->error;
                }
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ? AND dept_id = ?");
                $stmt->bind_param("ii", $id, $dept_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Subject deleted successfully.";
                } else {
                    $_SESSION['error'] = "Error: " . $stmt->error;
                }
                break;
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Call the function to handle subject operations
handleSubjectOperation($conn, $dept_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Management</title>
    <!-- Include your CSS files here -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container-fluid" style="margin-top:100px;">
    <div class="col-lg-14">
        <div class="row">
            <!-- Table Panel -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <b>Subject List</b>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#subjectModal">
                            <i class="fa fa-plus"></i> New Entry
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

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
                                            <button class="btn btn-sm btn-primary edit_subject" type="button" data-id="<?php echo $row['id']; ?>" data-subject="<?php echo $row['subject']; ?>" data-description="<?php echo $row['description']; ?>" data-units="<?php echo $row['total_units']; ?>" data-lecunits="<?php echo $row['Lec_Units']; ?>" data-labunits="<?php echo $row['Lab_Units']; ?>" data-course="<?php echo $row['course']; ?>" data-year="<?php echo $row['year']; ?>" data-semester="<?php echo $row['semester']; ?>" data-special="<?php echo $row['specialization']; ?>" data-hours="<?php echo $row['hours']; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="" method="POST" style="display: inline;">
                                                <input type="hidden" name="operation" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button class="btn btn-sm btn-danger delete_subject" type="submit" onclick="return confirm('Are you sure you want to delete this subject?');">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
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
                        <form action="" method="POST" id="manage-subject">
                            <input type="hidden" name="operation" value="save">
                            <div class="modal-body">
                                <input type="hidden" name="id">
                                <!-- Add all your form fields here -->
                                <!-- ... -->
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

<!-- Include your JS files here -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#subjectTable').DataTable();

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

        $('#subjectModal').modal('show');
    });

    // Filter functionality
    $('#filter-course, #filter-semester').change(function() {
        filterTable();
    });

    function filterTable() {
        const course = $('#filter-course').val();
        const semester = $('#filter-semester').val();

        $('.subject-row').each(function() {
            const rowCourse = $(this).data('course');
            const rowSemester = $(this).data('semester');
            
            if ((course === '' || course === rowCourse) && (semester === '' || semester === rowSemester)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});
</script>

</body>
</html>