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

$error = "";
$msg = "";

// Function to send reset email
function sendResetEmail($email, $reset_token) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Change this to your email
        $mail->Password = 'your-email-password';  // Change this to your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        //Recipients
        $mail->setFrom('no-reply@mccschedsystem.com', 'MCC SCHED SYSTEM ADMIN');
        $mail->addAddress($email);

        //Reset link
        $resetLink = 'http://localhost/SCHED4/admin/reset_password.php?email=' . urlencode($email) . '&token=' . $reset_token;

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset your password for MCC SCHED-SYSTEM';
        $mail->Body = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                }
                .container {
                    width: 80%;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }
                .button {
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <p>Hello,</p>
                <p>We received a request to reset your password. Click the button below to reset it:</p>
                <p><a href='" . $resetLink . "' class='button'>Reset Password</a></p>
                <p>If you did not request a password reset, please ignore this email.</p>
            </div>
        </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
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
        $reset_token = bin2hex(random_bytes(10));
        // Update the reset token in the database
        $updateQuery = "UPDATE users SET reset_token = '$reset_token' WHERE email = '$email'";

        if (mysqli_query($conn, $updateQuery)) {
            // Send the reset email
            if (sendResetEmail($email, $reset_token)) {
                // Display success message
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
                // Error sending email
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
            // SQL error
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
        // No email found
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
        /* Main layout adjustments */
        body {
            background-color: #f4f4f4;
            font-family: 'Source Sans Pro', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            margin: 20px;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .card-header {
            background-color: lightgray;
            color: black;
            text-align: center;
            padding: 1.5rem;
            border-radius: 20px 20px 0 0;
        }

        .h1 {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .card-body {
            padding: 2rem;
        }

        .input-group-text {
            background-color: #f4f4f4;
        }

        .btn {
            background-color: #007bff;
            border: none;
        }

        /* Logo styling */
        #logo-img {
            width: 5em;
            height: 5em;
            object-fit: cover;
            object-position: center center;
            border-radius: 50%;
        }

        /* Make the layout responsive */
        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }

            .h1 {
                font-size: 1.5rem;
            }

            #logo-img {
                width: 4em;
                height: 4em;
            }

            .btn {
                padding: 0.75rem 1rem;
            }

            .login-box {
                margin: 10px;
            }
        }
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

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
