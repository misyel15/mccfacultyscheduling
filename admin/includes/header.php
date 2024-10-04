<?php 
include 'db_connect.php'; 

// Check if the user is logged in and has a dept_id
if (!isset($_SESSION['username']) || !isset($_SESSION['dept_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Logout functionality
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Mcc Faculty Scheduling</title>
    <link rel="icon" href="assets/uploads/mcclogo.jpg" type="image/png">

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">

    <style>
        .header-mobile {
            position: fixed;
            top: 0;
            left: 0;
            height: 20px;
            width: 100%;
            z-index: 9999;
        }

        .header-mobile__bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 10px 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-desktop {
            position: fixed;
            margin-top: 0px;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            background-color: #f8f9fa;
        }

        .noti-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .account-wrap {
            display: flex;
            align-items: center;
        }

        .account-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 10px;
            min-width: 200px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .image img {
            border-radius: 50%;
            width: 50px;
            height: 40px;
        }
    </style>
</head>

<body class="animsition">
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <div>
                            <img src="assets/uploads/mcclogo.jpg" style="height: 50px; width: 50px;" alt="Mcc Faculty Scheduling" />
                            Mcc Faculty Scheduling
                        </div>
                        <button class="hamburger hamburger--slider" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <nav class="navbar-mobile">
                <div class="container-fluid">
                    <ul class="navbar-mobile__list list-unstyled">
                        <li><a href="home.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                        <li><a href="courses.php"><i class="fas fa-chart-bar"></i>Course</a></li>
                        <li><a href="subjects.php"><i class="fas fa-table"></i>Subject</a></li>
                        <li><a href="faculty.php"><i class="far fa-check-square"></i>Faculty</a></li>
                        <li><a href="room.php"><i class="fas fa-calendar-alt"></i>Room</a></li>
                        <li><a href="timeslot.php"><i class="fas fa-map-marker-alt"></i>Timeslot</a></li>
                        <li><a href="section.php"><i class="fas fa-map-marker-alt"></i>Section</a></li>
                        <li><a href="roomassigntry.php"><i class="fas fa-map-marker-alt"></i>Room Assigment</a></li>
                        <li><a href="roomsched.php"><i class="fas fa-map-marker-alt"></i>Room Schedule</a></li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-copy"></i>Other Reports</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li><a href="class_sched.php"><i class="fas fa-table"></i>Class Schedule</a></li>
                                <li><a href="load.php"><i class="fas fa-table"></i>Instructor's Load</a></li>
                                <li><a href="summary.php"><i class="fas fa-table"></i>Summary</a></li>
                                <li><a href="export.php"><i class="fas fa-table"></i>Export CSV</a></li>
                            </ul>
                        </li>
                        <li><a href="users.php"><i class="fas fa-map-marker-alt"></i>User</a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <img src="assets/uploads/mcclogo.jpg" style="height: 50px; width: 50px;" alt="Mcc Faculty Scheduling" />
                Mcc Faculty Scheduling
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li><a href="home.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                        <li><a href="courses.php"><i class="fas fa-chart-bar"></i>Course</a></li>
                        <li><a href="subjects.php"><i class="fas fa-table"></i>Subject</a></li>
                        <li><a href="faculty.php"><i class="far fa-check-square"></i>Faculty</a></li>
                        <li><a href="room.php"><i class="fas fa-calendar-alt"></i>Room</a></li>
                        <li><a href="timeslot.php"><i class="fas fa-map-marker-alt"></i>Timeslot</a></li>
                        <li><a href="section.php"><i class="fas fa-map-marker-alt"></i>Section</a></li>
                        <li><a href="roomassigntry.php"><i class="fas fa-map-marker-alt"></i>Room Assigment</a></li>
                        <li><a href="roomsched.php"><i class="fas fa-map-marker-alt"></i>Room Schedule</a></li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-copy"></i>Other Reports</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li><a href="class_sched.php"><i class="fas fa-table"></i>Class Schedule</a></li>
                                <li><a href="load.php"><i class="fas fa-table"></i>Instructor's Load</a></li>
                                <li><a href="summary.php"><i class="fas fa-table"></i>Summary</a></li>
                                <li><a href="export.php"><i class="fas fa-table"></i>Export CSV</a></li>
                            </ul>
                        </li>
                        <li><a href="users.php"><i class="fas fa-map-marker-alt"></i>User</a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <form class="form-header" action="" method="POST">
                            </form>
                            <div class="header-button">
                                <div class="noti-wrap">
                                    <div class="noti__item js-item-menu" style="fixed;">
                                        <i class="zmdi zmdi-notifications"></i>
                                        <span class="quantity">3</span>
                                        <div class="notifi-dropdown js-dropdown">
                                            <div class="notifi__title">
                                                <p>You have 3 Notifications</p>
                                            </div>
                                            <!-- Add notification items here -->
                                        </div>
                                    </div>
                                </div>
                                <div class="account-wrap float-right">
                                    <div class="account-item clearfix js-item-menu">
                                        <div class="content">
                                            <i class="fas fa-user"></i> 
                                            <?php echo $_SESSION['name']; ?>
                                        </div>
                                        <div class="account-dropdown js-dropdown">
                                            <div class="info clearfix" style="display: flex; align-items: center;">
                                                <i class="fas fa-user" style="font-size: 24px; margin-right: 10px;"></i> 
                                                <div class="content">
                                                    <h5 class="name" style="font-size: 17px; margin-left: -20%;">
                                                        <?php echo $_SESSION['name']; ?>
                                                    </h5>
                                                    <span class="email" style="font-size: 14px; color: #888;">
                                                        <?php echo isset($_SESSION['login_email']) ? $_SESSION['login_email'] : ''; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="account-dropdown__footer">
                                                <a href="?action=logout">
                                                    <i class="zmdi zmdi-power"></i>Logout
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- END HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <!-- Add your main content here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js"></script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js"></script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

    <script>
        // Disable right-click
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        }, false);

        // Disable F12 (Inspect Element)
        document.addEventListener('keydown', function (e) {
            // F12
            if (e.keyCode === 123) {
                e.preventDefault();
            }
            // Ctrl + Shift + I
            if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
                e.preventDefault();
            }
        }, false);

        // Disable Ctrl + U (View Source)
        document.addEventListener('keydown', function (e) {
            // Ctrl + U
            if (e.ctrlKey && e.keyCode === 85) {
                e.preventDefault();
            }
        }, false);
    </script>
</body>
</html>