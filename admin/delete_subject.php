<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Delete subject based on ID
    $sql = "DELETE FROM subjects WHERE id = '$id'";

    if ($conn->query($sql)) {
        // Redirect back to the main page
        header("Location: index.php");
    } else {
        echo "<script>
                Swal.fire('Error!', 'There was an error deleting the subject.', 'error')
                .then(function() {
                    window.history.back();
                });
              </script>";
    }
}
?>
