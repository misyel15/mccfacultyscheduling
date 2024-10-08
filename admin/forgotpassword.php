<?php
session_start();
include("db_connect.php");

$error = "";
$msg = "";

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    $token = mysqli_real_escape_string($conn, $_GET['token']);

    // Validate the reset token and email
    $query = "SELECT * FROM users WHERE email='$email' AND reset_token='$token'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // If token and email are valid, check if the form is submitted
        if (isset($_POST['update_password'])) {
            $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
            $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

            if ($new_password === $confirm_password) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database
                $update_query = "UPDATE users SET password='$hashed_password', reset_token=NULL WHERE email='$email'";

                if (mysqli_query($conn, $update_query)) {
                    $msg = "Your password has been updated successfully!";
                    echo '<script>
                            window.onload = function() {
                                Swal.fire({
                                    title: "Success!",
                                    text: "' . $msg . '",
                                    icon: "success",
                                    allowOutsideClick: false,
                                    confirmButtonText: "Login"
                                }).then(function() {
                                    window.location = "index.php";
                                });
                            };
                          </script>';
                } else {
                    $error = "Failed to update password. Please try again later.";
                }
            } else {
                $error = "Passwords do not match!";
            }
        }
    } else {
        $error = "Invalid or expired token!";
    }
} else {
    $error = "Invalid request!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body>
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a class="h1"><b>Reset</b>|Password</a>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <p class="login-box-msg">Enter your new password below.</p>
            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" name="update_password" class="btn btn-primary btn-block">Update Password</button>
                    </div>
                </div>
            </form>
            <p class="mt-3 mb-1">
                <a href="index.php">Login</a>
            </p>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
