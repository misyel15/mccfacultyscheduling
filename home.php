<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>MCC Faculty Scheduling System</title>
  <link rel="icon" href="back.png" type="image/png">

  <!-- Include Bootstrap for styling -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
  <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
  <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

  <?php include('./header.php'); ?>

  <?php 
    if (isset($_SESSION['login_id'])) {
      header("location:index.php");
    }
  ?>
</head>

<style>
  body {
    width: 100%;
    height: 100vh;
    margin: 0;
    overflow: hidden;
    background-image: url('back.png'); /* Replace with your image */
    background-size: cover;
    background-position: center;
  }

  #main {
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #login {
    width: 100%;
    max-width: 400px;
  }

  .card {
    background: rgba(255, 255, 255, 0.85); /* Transparent background */
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
  }

  .navbar-brand {
    color: white;
    font-size: 1rem;
    font-weight: bold;
  }

  .navbar {
    background-color: rgba(0, 0, 0, 0.6); /* Slightly transparent navbar */
  }

  .back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: none;
  }
</style>

<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="#">
      <img src="back.png" alt="Logo" style="width: 40px; height: 30px; margin-right: 10px;">
      MCC Faculty Scheduling System
    </a>
  </nav>

  <main id="main">
    <div id="login">
      <div class="card">
        <div class="card-body">
          <form id="login-form">
            <h4 class="text-center"><b>Welcome to Faculty Scheduling System</b></h4>
            <div class="form-group">
              <label for="id_no" class="control-label">Enter Your Faculty ID No.</label>
              <input type="text" id="id_no" name="id_no" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
          </form>
          <br>
        </div>
      </div>
    </div>
  </main>

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

  <!-- Include Bootstrap JS and jQuery for functionality -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    // Smooth scroll to top
    $(window).scroll(function () {
      if ($(this).scrollTop() > 100) {
        $('.back-to-top').fadeIn();
      } else {
        $('.back-to-top').fadeOut();
      }
    });

    $('.back-to-top').click(function () {
      $('html, body').animate({ scrollTop: 0 }, 600);
      return false;
    });

    // Login form functionality
    $('#login-form').submit(function (e) {
      e.preventDefault();
      const button = $(this).find('button[type="submit"]');
      button.attr('disabled', true).text('Logging in...');

      if ($(this).find('.alert-danger').length > 0) {
        $(this).find('.alert-danger').remove();
      }

      $.ajax({
        url: 'admin/ajax.php?action=login_faculty',
        method: 'POST',
        data: $(this).serialize(),
        error: function (err) {
          console.error(err);
          button.removeAttr('disabled').text('Login');
        },
        success: function (resp) {
          if (resp == 1) {
            location.href = 'index.php';
          } else {
            $('#login-form').prepend('<div class="alert alert-danger">ID Number is incorrect.</div>');
            button.removeAttr('disabled').text('Login');
          }
        }
      });
    });
  </script>

</body>
</html>
