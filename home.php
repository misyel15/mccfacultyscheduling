<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>MCC Faculty Scheduling System</title>
  <link rel="icon" href="mcclogo.jpg" type="image/png">

  <!-- Include Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet">
  <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet">
  <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet">

  <?php 
    session_start();
    if (isset($_SESSION['login_id'])) {
      header("Location: index.php");
      exit;
    }
  ?>

  <style>
    body {
      width: 100%;
      height: 100%;
      background-color: #343a40;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .navbar {
      background-color: #343a40;
    }

    .navbar-brand {
      color: white;
      font-size: 1.2rem;
      font-weight: bold;
    }

    .navbar-brand img {
      width: 40px;
      height: 40px;
      margin-right: 10px;
    }

    .card {
      width: 100%;
      max-width: 400px;
      border-radius: 10px;
    }
  </style>
</head>

<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">
      <img src="mcclogo.jpg" alt="MCC Logo"> MCC Faculty Scheduling System
    </a>
  </nav>

  <div class="card">
    <div class="card-body">
      <form id="login-form">
        <h4 class="text-center"><b>Welcome to the Faculty Scheduling System</b></h4>
        <div class="form-group">
          <label for="id_no">Enter Your Faculty ID No.</label>
          <input type="text" id="id_no" name="id_no" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
      </form>
      <div id="alert-message"></div>
    </div>
  </div>

  <!-- Include Bootstrap JS and jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    $('#login-form').submit(function (e) {
      e.preventDefault();
      const loginButton = $('#login-form button[type="submit"]');
      loginButton.attr('disabled', true).text('Logging in...');

      $.ajax({
        url: 'admin/ajax.php?action=login_faculty',
        method: 'POST',
        data: $(this).serialize(),
        success: function (resp) {
          if (resp == 1) {
            window.location.href = 'index.php';
          } else {
            $('#alert-message').html('<div class="alert alert-danger mt-3">ID Number is incorrect.</div>');
            loginButton.removeAttr('disabled').text('Login');
          }
        },
        error: function (err) {
          console.error(err);
          $('#alert-message').html('<div class="alert alert-danger mt-3">An error occurred. Please try again.</div>');
          loginButton.removeAttr('disabled').text('Login');
        }
      });
    });
  </script>

</body>

</html>
