<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "classes/AllClasses.php";
require_once "functions/time.php";
require_once "functions/routing.php";
require_once "functions/seolink.php";

// error_reporting(E_ALL);

// DATABASE - SESSION VE TOKEN BAĞLATILARI
$db = new aybu\db\mysqlDB();
$SS = new aybu\session\session();
$token = new aybu\token\token();

use aybu\token\token as Token;
use aybu\session\session as Session;

// Dil hangisi?

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}
// Dili çek
require_once "languages/language_" . $language . ".php";

$adminid = $SS->get("AdminID"); // Kullanıcı ID'sini al

$control = $db->getColumnData("SELECT AdminConfirm FROM admins WHERE AdminID = ?", array($adminid));

if ($control != 1) {
    $SS->create("LogedIn", false);
    $SS->del("AdminID");
    $gopage = ($language == 'en' ? 'login' : 'giris');
    go("http://localhost/aybu/socialmedia/" . $gopage . "");
}

// LogedIn adında session yoksa 404.php ye yönlendir
if(!$SS->get("AdminLogedIn")){
  header("Location: http://localhost/aybu/socialmedia/404.php");
}

if (!defined("aybupages.?")) {
  header("Location: http://localhost/aybu/socialmedia/404.php");
}


date_default_timezone_set('Europe/Istanbul');

$page = $_GET["page"];
$part = $_GET["part"];
$edit = $_GET["edit"];

$profile_photo = $db->getColumnData("SELECT AdminImg FROM admins WHERE AdminID = ?", array($adminid));
$gender = $db->getColumnData("SELECT AdminGender FROM admins WHERE AdminID = ?", array($adminid));

if (is_null($profile_photo)) {
    if ($gender == 'Erkek') {
        $profile_photo = "profilemale.png";
    } else {
        $profile_photo = "profilefemale.png";
    }
}

$user_name = $db->getColumnData("SELECT AdminName FROM admins WHERE AdminID = $adminid ");
$user_lastname = $db->getColumnData("SELECT AdminLastname FROM admins WHERE AdminID = $adminid ");
$user_namelastname = $user_name . " " . $user_lastname;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" href="images/logo.png" type="image/x-icon" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>AYBU | Admin Paneli</title>
    <script src="https://kit.fontawesome.com/913cc5242f.js" crossorigin="anonymous"></script>
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/adminstyle.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/baguetteBox.min.css" />
    <script src="ckeditor/ckeditor.js"></script>
    <script src="js/baguetteBox.min.js"></script>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery-ui.js"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar-->
        <div class="emptysidebar" style="width:286px;"></div>
        <div class="border-end bg-white position-fixed" id="sidebar-wrapper" style="z-index:3">
            <div class="sidebar-heading border-bottom bg-light" id="sidebar_user"><img src="images_profile/<?= $profile_photo ?>" class="rounded-circle me-1" width="40" height="40"><?= $user_namelastname ?></div>
            <div class="list-group list-group-flush">
                <ul class="nav nav-fill d-flex flex-column" id="myTab" role="tablist">
                    <?php if ($adminid == 1) { ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active list-group-item list-group-item-action list-group-item-light p-3 text-start" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab" parttitle="Dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link list-group-item list-group-item-action list-group-item-light p-3 text-start" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins" type="button" role="tab" parttitle="Admins">Admins</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link list-group-item list-group-item-action list-group-item-light p-3 text-start" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button" role="tab" parttitle="Members">Members</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link list-group-item list-group-item-action list-group-item-light p-3 text-start" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" parttitle="Posts">Posts</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link list-group-item list-group-item-action list-group-item-light p-3 text-start" id="topics-tab" data-bs-toggle="tab" data-bs-target="#topics" type="button" role="tab" parttitle="Topics">Topics</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link list-group-item list-group-item-action list-group-item-light p-3 text-start" id="news-tab" data-bs-toggle="tab" data-bs-target="#news" type="button" role="tab" parttitle="News">News</a>
                    </li>
                    <li class="nav-item" style="position: absolute;bottom:0px;width:100%" role="presentation">
                        <a class="nav-link bg-secondary text-light list-group-item list-group-item-action list-group-item-light p-3 text-start" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" parttitle="Password">Password</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="togglemenu d-flex justify-content-center align-items-center position-fixed" style="height:100vh;left:240px;z-index:5">
            <div style="position:absolute;z-index:5;cursor:pointer;" id="sidebarToggle" state="open"><i class="fas fa-chevron-circle-left fa-2x"></i></div>
        </div>
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom" id="navbar_top" style="z-index: 1;">
                <div class="container-fluid">
                    <h4 id="part_title" class="py-2 m-0">Dashboard</h4>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0 align-items-center">
                            <li class="nav-item ms-md-3">
                                <a class="text-decoration-none text-dark" style="cursor: pointer;" href="http://localhost/aybu/socialmedia/exit.php">
                                    <i class="fas fa-sign-out-alt" style="font-size: 18px;">Exit</i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page content-->
            <div class="container-fluid">
                <div class="tab-content" id="myTabContent">
                    <?php if ($adminid == 1) { ?>
                        <div class="tab-pane fade show active p-3" id="dashboard" role="tabpanel"><?php require_once "dashboard.php"; ?></div>
                        <div class="tab-pane fade p-3" id="admins" role="tabpanel"><?php require_once "admins.php"; ?></div>
                        <div class="tab-pane fade p-3" id="members" role="tabpanel"><?php require_once "members.php"; ?></div>
                    <?php } ?>
                    <div class="tab-pane fade p-3" id="posts" role="tabpanel"><?php require_once "posts.php"; ?></div>
                    <div class="tab-pane fade p-3" id="topics" role="tabpanel"><?php require_once "topics.php"; ?></div>
                    <div class="tab-pane fade p-3" id="news" role="tabpanel"><?php require_once "news.php"; ?></div>
                    <div class="tab-pane fade p-3" id="password" role="tabpanel"><?php require_once "password.php"; ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once "functions/ajaxadminfunctions.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script>
        var sidebar_height = $("#sidebar_user").height();
        sidebar_height = sidebar_height + 29;
        $("#navbar_top").css("height", sidebar_height);
        $(".list-group-item-action").on("click", function() {
            var parttitle = $(this).attr("parttitle");
            $("#part_title").html(parttitle);
        });
        $("#sidebarToggle").on("click", function() {
            var thisval = $(this).attr("state");
            if (thisval == 'open') {
                $(this).html('<i class="fas fa-chevron-circle-right fa-2x"></i>');
                $(this).attr("state", "close");
                $('.togglemenu').animate({
                    left: '-=240'
                }, 250, 'swing');
                $('.emptysidebar').animate({
                    width: '0px'
                }, 250, 'swing');
            } else if (thisval == 'close') {
                $(this).html('<i class="fas fa-chevron-circle-left fa-2x"></i>');
                $(this).attr("state", "open");
                $('.togglemenu').animate({
                    left: '+=240'
                }, 250, 'swing');
                $('.emptysidebar').animate({
                    width: '286px'
                }, 250, 'swing');
            }
        });
    </script>
</body>

</html>