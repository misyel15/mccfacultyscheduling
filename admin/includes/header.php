<?php 
include 'db_connect.php'; 

// Check if the user is logged in and has a dept_id
if (!isset($_SESSION['username']) || !isset($_SESSION['dept_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
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
    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom CSS */
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
                            Mcc Faculty Scheduling<!-- HEADER MOBILE -->
<header class="header-mobile">
    <div class="hamburger-container">
        <button class="hamburger hamburger--slider" type="button" id="hamburger-btn">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>
    </div>
</header>

<!-- Mobile Navbar -->
<nav class="navbar-mobile" id="navbar-mobile">
    <ul class="navbar-mobile__list list-unstyled">
        <li><a href="home.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="courses.php"><i class="fas fa-book-open"></i> Courses</a></li>
        <li><a href="subjects.php"><i class="fas fa-book"></i> Subject</a></li>
        <li><a href="faculty.php"><i class="fas fa-user-tie"></i> Faculty</a></li>
        <li><a href="room.php"><i class="fas fa-door-closed"></i> Room</a></li>
        <li><a href="timeslot.php"><i class="fas fa-clock"></i> Timeslot</a></li>
        <li><a href="section.php"><i class="fas fa-users"></i> Section</a></li>
        <li><a href="roomassigntry.php"><i class="fas fa-tasks"></i> Room Assignment</a></li>
        <li><a href="roomsched.php"><i class="fas fa-calendar-alt"></i> Room Schedule</a></li>
        <li><a href="users.php"><i class="fas fa-users-cog"></i> User</a></li>
    </ul>
</nav>

<!-- Desktop Sidebar -->
<aside class="menu-sidebar d-none d-lg-block">
    <div class="logo">
        <img src="assets/uploads/mcclogo.jpg" alt="MCC Faculty Scheduling" style="height: 50px; width: 50px;" />
        <h2 style="color: white;">MCC Faculty Scheduling</h2>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <ul class="list-unstyled navbar__list">
            <li><a href="home.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="courses.php"><i class="fas fa-book-open"></i> Courses</a></li>
            <li><a href="subjects.php"><i class="fas fa-book"></i> Subject</a></li>
            <li><a href="faculty.php"><i class="fas fa-user-tie"></i> Faculty</a></li>
            <li><a href="room.php"><i class="fas fa-door-closed"></i> Room</a></li>
            <li><a href="timeslot.php"><i class="fas fa-clock"></i> Timeslot</a></li>
            <li><a href="section.php"><i class="fas fa-users"></i> Section</a></li>
            <li><a href="roomassigntry.php"><i class="fas fa-tasks"></i> Room Assignment</a></li>
            <li><a href="roomsched.php"><i class="fas fa-calendar-alt"></i> Room Schedule</a></li>
            <li><a href="users.php"><i class="fas fa-users-cog"></i> User</a></li>
        </ul>
    </div>
</aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop d-none d-lg-block">
                <div class="header-desktop__item">
                    <div class="header-desktop__logo">
                        <a href="home.php">
                            <img src="assets/uploads/mcclogo.jpg" style="height: 50px; width: 50px;" alt="Mcc Faculty Scheduling" />
                        </a>
                    </div>
                    <div class="noti-wrap">
                        <div class="noti__item js-item-menu">
                            <i class="fas fa-bell"></i>
                            <span class="quantity">3</span>
                            <div class="notifi-dropdown js-dropdown">
                                <div class="notifi__title">
                                    <p>You have 3 Notifications</p>
                                </div>
                                <div class="notifi__item">
                                    <div class="bg-c1 img-cir img-40">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="content">
                                        <p>Your request has been approved</p>
                                        <span class="date">April 12, 2024</span>
                                    </div>
                                </div>
                                <div class="notifi__item">
                                    <div class="bg-c2 img-cir img-40">
                                        <i class="fas fa-exclamation"></i>
                                    </div>
                                    <div class="content">
                                        <p>Your submission is under review</p>
                                        <span class="date">April 11, 2024</span>
                                    </div>
                                </div>
                                <div class="notifi__item">
                                    <div class="bg-c3 img-cir img-40">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div class="content">
                                        <p>Your request has been denied</p>
                                        <span class="date">April 10, 2024</span>
                                    </div>
                                </div>
                                <div class="notifi__footer">
                                    <a href="#">All notifications</a>
                                </div>
                            </div>
                        </div>
                        <div class="account-wrap">
                            <div class="image">
                                <img src="assets/uploads/mcclogo.jpg" alt="User" />
                            </div>
                            <div class="content">
                                <a class="js-acc-btn" href="#"><?php echo $_SESSION['username']; ?></a>
                            </div>
                            <div class="account-dropdown js-dropdown">
                                <div class="info clearfix">
                                    <div class="image">
                                        <a href="#">
                                            <img src="assets/uploads/mcclogo.jpg" alt="User" />
                                        </a>
                                    </div>
                                    <div class="content">
                                        <h5 class="name">
                                            <a href="#"><?php echo $_SESSION['username']; ?></a>
                                        </h5>
                                        <span class="email"><?php echo $_SESSION['email']; ?></span>
                                    </div>
                                </div>
                                <div class="account-dropdown__body">
                                    <div class="account-dropdown__item">
                                        <a href="profile.php">
                                            <i class="fas fa-user"></i> Profile
                                        </a>
                                    </div>
                                    <div class="account-dropdown__item">
                                        <a href="settings.php">
                                            <i class="fas fa-cog"></i> Settings
                                        </a>
                                    </div>
                                </div>
                                <div class="account-dropdown__footer">
                                    <a href="logout.php">
                                        <i class="fas fa-sign-out-alt"></i> Log Out
                                    </a>
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
                        <h2 class="title-1">Welcome to Mcc Faculty Scheduling</h2>
                        <p>Your dashboard content goes here.</p>
                    </div>
                </div>
            </div>
            <!-- END MAIN CONTENT-->
        </div>
        <!-- END PAGE CONTAINER-->
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/slick/slick.min.js"></script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <!-- Main JS-->
    <script src="js/main.js"></script>
</body>

</html>
