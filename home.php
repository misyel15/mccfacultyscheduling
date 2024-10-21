<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>MCC Faculty Scheduling System</title>
  <link rel="icon" href="mcclogo.jpg" type="image/png">

  <!-- Include Bootstrap for styling -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
  <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
  <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

  <?php include('./header.php'); ?>

  <?php 
    if (isset($_SESSION['login_id'])) 
      header("location:index.php");
  ?>

</head>

<style>
  body {
    width: 100%;
    height: 100vh;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #343a40;
  }

  #main {
    width: 100%;
    max-width: 400px;
  }

  .navbar {
    background-color: #007bff;
  }

  .navbar-brand {
    display: flex;
    align-items: center;
    color: white;
    font-size: 1.1rem;
    font-weight: bold;
  }

  .navbar-brand img {
    width: 40px;
    height: 40px;
    margin-right: 10px;
  }

  .card {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .form-group label {
    font-weight: 500;
  }

  .btn-primary {
    background-color: #007bff;
    border: none;
  }

  .btn-primary:hover {
    background-color: #0056b3;
  }

  .back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: none;
    font-size: 1.5rem;
  }

  .back-to-top i {
    color: #007bff;
  }
</style>

<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="#">
      <img src="mcclogo.jpg" alt="Logo" style="width: 40px; height: 30px; margin-right: 10px;">Mcc Faculty Scheduling System
</a>
  </nav>

  <main id="main">
    <div id="login">
      <div class="card">
        <div class="card-body">
          <form id="login-form">
            <h4 class="text-center"><b>Welcome to Faculty Scheduling System</b></h4>
            <div class="form-group mt-4">
              <label for="id_no" class="control-label">Please enter your Faculty ID No.</label>
              <input type="text" id="id_no" name="id_no" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-4">Login</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <a href="#" class="back-to-top"><i class="fa fa-arrow-up"></i></a>

  <!-- Include Bootstrap JS and jQuery for functionality -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    // Smooth scrolling to top when 'back-to-top' button is clicked
    $(window).scroll(function () {
      if ($(this).scrollTop() > 100) {
        $('.back-to-top').fadeIn();
      } else {
        $('.back-to-top').fadeOut();
      }
    });

    $('.back-to-top').click(function () {
      $('html, body').animate({ scrollTop: 0 }, 800);
      return false;
    });

    // Login form submission
    $('#login-form').submit(function (e) {
      e.preventDefault();
      const loginButton = $(this).find('button[type="submit"]');
      loginButton.prop('disabled', true).text('Logging in...');

      if ($(this).find('.alert-danger').length > 0) {
        $(this).find('.alert-danger').remove();
      }

      $.ajax({
        url: 'admin/ajax.php?action=login_faculty',
        method: 'POST',
        data: $(this).serialize(),
        error: (err) => {
          console.error(err);
          loginButton.prop('disabled', false).text('Login');
        },
        success: function (resp) {
          if (resp == 1) {
            location.href = 'index.php';
          } else {
            $('#login-form').prepend('<div class="alert alert-danger">ID Number is incorrect.</div>');
            loginButton.prop('disabled', false).text('Login');
          }
        }
      });
    });
  </script>

</body>

</html>
