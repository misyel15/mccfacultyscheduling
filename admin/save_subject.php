<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $total_units = $_POST['total_units'];
    $Lec_Units = $_POST['Lec_Units'];
    $Lab_Units = $_POST['Lab_Units'];
    $hours = $_POST['hours'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $specialization = $_POST['specialization'];

    // Insert or update the subject based on whether ID exists
    if ($id) {
        // Update existing record
        $sql = "UPDATE subjects SET 
                subject = '$subject', description = '$description', total_units = '$total_units',
                Lec_Units = '$Lec_Units', Lab_Units = '$Lab_Units', hours = '$hours', 
                course = '$course', year = '$year', semester = '$semester', 
                specialization = '$specialization'
                WHERE id = '$id'";
    } else {
        // Insert new record
        $sql = "INSERT INTO subjects (subject, description, total_units, Lec_Units, Lab_Units, hours, course, year, semester, specialization) 
                VALUES ('$subject', '$description', '$total_units', '$Lec_Units', '$Lab_Units', '$hours', '$course', '$year', '$semester', '$specialization')";
    }

    if ($conn->query($sql)) {
        // Redirect to main page after successful save
        header("Location: index.php");
    } else {
        echo "<script>
                Swal.fire('Error!', 'There was an error saving the subject.', 'error')
                .then(function() {
                    window.history.back();
                });
              </script>";
    }
}
?>
