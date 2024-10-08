<?php
session_start();
include("db_connect.php");
include 'includes/style.php'; 
include 'includes/head.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Function to send reset email
function sendResetEmail($email, $reset_token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        
        // Use environment variables to store sensitive credentials
        $mail->Username = getenv("EMAIL_USER="your-email@gmail.com"); // Set your email via environment variable
        $mail->Password = getenv("EMAIL_PASS="your-email-password"s);  // Set your email password via environment variable

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('no-reply@mccschedsystem.com', 'MCC SCHED SYSTEM ADMIN');
        $mail->addAddress($email);

        // Reset link
        $resetLink = 'http://yourdomain.com/admin/reset_password.php?email=' . urlencode($email) . '&token=' . $reset_token;

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Reset your password for MCC SCHED-SYSTEM';
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <div style='margin: 20px;'>
                <h2>Password Reset Request</h2>
                <p>Hi,</p>
                <p>We received a request to reset your password. Click the button below to reset it:</p>
                <p><a href='" . $resetLink . "' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none;'>Reset Password</a></p>
                <p>If you didn't request this, please ignore this email.</p>
            </div>
        </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Function to handle password reset
function processPasswordReset($conn, $email) {
    // Check if email exists
    $email = mysqli_real_escape_string($conn, $email);
    $checkQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkQuery);

    if ($result && mysqli_num_rows($result) == 1) {
        // Generate a random token
        $reset_token = bin2hex(random_bytes(32)); // Use a longer token for better security

        // Update the reset token in the database
        $hashed_token = password_hash($reset_token, PASSWORD_BCRYPT);
        $updateQuery = "UPDATE users SET reset_token = '$hashed_token', token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = '$email'";

        if (mysqli_query($conn, $updateQuery)) {
            // Send the reset email
            if (sendResetEmail($email, $reset_token)) {
                echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Success!",
                            text: "A password reset link has been sent to your email.",
                            icon: "success"
                        });
                    };
                </script>';
            } else {
                echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to send reset link. Please try again later.",
                            icon: "error"
                        });
                    };
                </script>';
            }
        } else {
            echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to update reset token.",
                        icon: "error"
                    });
                };
            </script>';
        }
    } else {
        echo '<script>
            window.onload = function() {
                Swal.fire({
                    title: "Error!",
                    text: "No account found with that email.",
                    icon: "error"
                });
            };
        </script>';
    }
}

// Handling the form submission
if (isset($_POST['reset'])) {
    $email = $_POST['email'];
    processPasswordReset($conn, $email);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Forgot Password</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
        /* Styling omitted for brevity */
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <center><img src="assets/uploads/back.png" alt="System Logo" class="img-thumbnail rounded-circle" id="logo-img"></center>
            <a class="h1"><b>Retrieve</b>|Account</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" name="reset" class="btn btn-primary btn-block">Request new password</button>
                    </div>
                </div>
            </form>
            <p class="mt-3 mb-1">
                <a href="index.php">Login</a>
            </p>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
