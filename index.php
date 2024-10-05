<?php include('admin/db_connect.php'); ?>
<!DOCTYPE html>
<html lang="en">

<?php session_start(); ?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>School Faculty Scheduling System</title>

  <?php
  if (!isset($_SESSION['login_id']))
    header('location:login.php');
  include('./header.php'); 
  ?>

  <link rel="stylesheet" href="path/to/bootstrap.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="path/to/bootstrap.bundle.min.js"></script>

  <style>
    body {
      background: #80808045;
    }

    /* Other styles remain unchanged... */
  </style>

</head>

<body>
  <?php include 'topbar.php' ?>
  <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white"></div>
  </div>
  <main id="" style="margin-top: 3.5rem;" class="bg-dark">
    <div class="container pt-4 pb-4">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <table class="table table-bordered table-condensed table-hover" id="insloadtable">
              <thead>
                <tr>
                  <th class="text-center">Code</th>
                  <th class="text-center">Descriptive Title</th>
                  <th class="text-center">Day</th>
                  <th class="text-center">Time</th>
                  <th class="text-center">Room</th>
                  <th class="text-center">Section</th>
                  <th class="text-center">Units (lec)</th>
                  <th class="text-center">Units (lab)</th>
                  <th class="text-center">Total Units</th>
                  <th class="text-center">Total Hours</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i = 1;
                $sumtu = 0;
                $sumh = 0;
                $faculty_id = $_SESSION['login_id'];
                $loads = $conn->query("SELECT * FROM loading where faculty='$faculty_id'");
                while ($lrow = $loads->fetch_assoc()) {
                  // Your existing loop code...
                }
                echo '<tfoot><tr style="height: 20px">
                  <td class="s4"></td>
                  <td class="s3"></td>
                  <td class="s3"></td>
                  <td class="s3"></td>
                  <td class="s3"></td>
                  <td class="s3"></td>
                  <td class="s10 softmerge">
                    <div class="text-center" style="width:150px;left:-1px">
                      <span style="font-weight:bold;">Total Number of Units/Hours(Basic)</span>
                    </div>
                  </td>
                  <td class="s11"></td>
                  <td class="text-center">' . $sumtu . '</td>
                  <td class="text-center">' . $sumh . '</td>
                </tr></tfoot>';
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <div id="preloader"></div>
  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Other modals remain unchanged -->

  <script>
    window.start_load = function() {
      $('body').prepend('<div id="preloader"></div>'); // Corrected ID
    }
    window.end_load = function() {
      $('#preloader').fadeOut('fast', function() {
        $(this).remove();
      });
    }
    // Other JavaScript functions remain unchanged...
    
    $(document).ready(function() {
      $('#preloader').fadeOut('fast', function() {
        $(this).remove();
      });
    });
  </script>
</body>

</html>
