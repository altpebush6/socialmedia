<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "classes/AllClasses.php";
require_once "functions/time.php";
require_once "functions/routing.php";
require_once "functions/getmonth.php";
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

$memberid = $SS->get("MemberID"); // Kullanıcı ID'sini al

$control = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($memberid));

if ($control != 1) {
  $SS->create("LogedIn", false);
  $SS->del("MemberID");
  $gopage = ($language == 'en' ? 'login' : 'giris');
  go("http://localhost/aybu/socialmedia/" . $gopage . "");
}

// // LogedIn adında session yoksa 404.php ye yönlendir
// if(!$SS->get("LogedIn")){
//   header("Location: http://localhost/aybu/socialmedia/404.php");
// }

if (!defined("aybupages.?")) {
  header("Location: http://localhost/aybu/socialmedia/404.php");
}

date_default_timezone_set('Europe/Istanbul');

$page = $_GET["page"];
$part = $_GET["part"];
$edit = $_GET["edit"];

if ($_SERVER['REQUEST_METHOD'] == 'GET') { // İNDİRME İŞLEMİ
  if (isset($_GET["download"]) && !empty($_GET["download"])) {
    $filename = $_GET["download"];
    $filePath = "post_files/" . $filename;
    if (file_exists($filePath)) {
      header("Content-Description: File Transfer");
      header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=" . $filename);
      header("Content-Transfer-Encoding:binary");
      header("Expires:0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Pragma: public");
      header("Content-Length:" . filesize($filePath));
      ob_clean();
      flush();
      readfile($filePath);
      exit();
    }
  }
}

// Anasayfa işlemleri
if ($page == $translates["home"]) {
  if ($part) {
    $isTopicHave = $db->getColumnData("SELECT TopicLink FROM topics WHERE TopicLink = ? AND TopicActive = ?", array($part, 1));
    if (empty($isTopicHave)) {
      header("Location: http://localhost/aybu/socialmedia/404.php");
    }
  }
}

// Haber sayfası işlemleri
if ($page == $translates["News"]) {
  if ($part) {
    $allnews = $db->getDatas("SELECT * FROM news");
    $statement = "";
    foreach ($allnews as $news) {
      $Header = $news->NewsHeader;
      $seodHeader = seolink($Header) . "-" . $news->NewsID;
      if ($seodHeader == $part) {
        $statement = "true";
      }
    }
    if ($statement == "") {
      header("Location: http://localhost/aybu/socialmedia/404.php");
    }
  }
}

// Profil sayfası işlemleri
if ($page == $translates["profile"]) {
  if ($part) {
    $isMemberHave = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($part));
    $isMemberActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($part));
    $lastid = $db->getColumnData("SELECT MemberID FROM members ORDER BY MemberID DESC LIMIT 1");
    if (empty($isMemberHave) or $isMemberActive != 1 or $lastid < $part or is_int($part)) {
      header("Location: http://localhost/aybu/socialmedia/404.php");
    }
  }
}

$cover_photo = $db->getColumnData("SELECT Member_Coverimg FROM images WHERE MemberID = ?", array($memberid));
$profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberid));
$gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($memberid));

if (is_null($profile_photo)) {
  if ($gender == 'Erkek') {
    $profile_photo = "profilemale.png";
  } else {
    $profile_photo = "profilefemale.png";
  }
}
if (is_null($cover_photo)) {
  $cover_photo = "noncover.png";
}

$user_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = $memberid ");
$user_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = $memberid ");
$user_namelastname = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = $memberid ");

$message_count = $db->getColumnData("SELECT COUNT(*) FROM chatbox WHERE MessageToID = ? AND MessageStatus = ? AND MessageHasRead = ?", array($memberid, 1, 0));
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <link rel="icon" href="images/logo.png" type="image/x-icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>
    <?php
    switch ($page) {
      case 'home':
      case 'anasayfa':
        echo "AYBU | " . $translates["Home"];
        break;
      case 'profile':
      case 'profil':
        if ($part) {
          $name_profile = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($part));
          echo "AYBU | " . $name_profile;
        } else {
          echo "AYBU | " . $user_namelastname;
        }
        break;
      case 'settings':
      case 'ayarlar':
        echo "AYBU | " . $translates["Settings"];
        break;
      case 'messages':
      case 'mesajlar':
        echo "AYBU | " . $translates["Messages"];
        break;
      case 'clubs':
      case 'kulupler':
        echo "AYBU | " . $translates["Clubs"];
        break;
      case 'findfriends':
      case 'arkadasara':
        echo "AYBU | " . $translates["Searchfriend"];
        break;
      default:
        echo "AYBU | " . $translates["socialmedia"];
        break;
    }

    ?>
  </title>
  <base href="http://localhost/aybu/socialmedia/">
  <link rel="stylesheet" href="css/owl.carousel.css" />
  <link rel="stylesheet" href="css/baguetteBox.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/bootstrap.css" />
  <link rel="stylesheet" href="css/croppie.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" type="text/javascript"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/baguetteBox.min.js"></script>
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/croppie.js"></script>
  <script>
    function LoadFinish() {
      $(".lds-ellipsis").css("display", "none");
      $("body").css("opacity", "1");
    }
  </script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://kit.fontawesome.com/913cc5242f.js" crossorigin="anonymous"></script>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Open+Sans:wght@300&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
  <!-- GOOGLE FONTS -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@200;400&family=Libre+Baskerville&family=Lora:wght@500&family=Merriweather&family=Nanum+Gothic&family=Roboto+Slab:wght@500&display=swap" rel="stylesheet">
  <!-- GOOGLE FONTS -->
</head>

<body onload="LoadFinish()" onload="ControlLogin(<?= $memberid ?>)">
  <div class="lds-ellipsis">
    <div></div>
    <div></div>
    <div></div>
    <div></div>
  </div>
  <!--NAVBARs-->
  <nav class="navbar navbar-expand-md px-3 navbar-light fixed-top bg-light rounded-1" id="navbar">
    <div class="container-fluid">
      <a class="navbar-brand text-light" href="http://localhost/aybu/socialmedia/<?= $translates["home"] ?>" style="font-family: 'Merriweather', serif;"> <img src="images/ybu_logo.png" height="60"><?= $user_name . " " . $user_lastname; ?></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse ps-md-4" id="navbarSupportedContent">
        <form class="d-flex mx-auto ps-md-5" method="post">
          <input class="form-control me-2 ms-md-5" type="text" name="search_person" id="search_person" autocomplete="off" placeholder="<?= $translates["search_friend"] ?>">
          <a href="" id="searchfriendicon" class="btn btn-outline-light" type="button">Search</a>
        </form>
        <ul class="navbar-nav mt-3 mt-md-0">
          <?php
          $list = $db->getDatas("SELECT * FROM navbar_$language LIMIT 4");
          foreach ($list as $items) {
            if ($page == $items->NavLink) {
              echo '<li class="nav-item my-2 my-md-auto mx-auto mx-md-2 d-flex justify-content-center"> <a class="nav-link text-light rounded-circle text-center" style="width:40px;height:40px;background:rgba(0, 0, 0, 0.39)" href="http://localhost/aybu/socialmedia/' . $items->NavLink . '">' . $items->NavIcon . '</a><a href="http://localhost/aybu/socialmedia/' . $items->NavLink . '" class="text-decoration-none ms-2 mt-2 d-md-none nav-name text-light">' . $items->NavName . '</a></li><hr>';
            } else {
              echo '<li class="nav-item my-2 my-md-auto mx-auto mx-md-2 d-flex justify-content-center"> <a class="nav-link text-dark bg-light rounded-circle text-center" style="width:40px;height:40px" href="http://localhost/aybu/socialmedia/' . $items->NavLink . '">' . $items->NavIcon . '</a><a href="http://localhost/aybu/socialmedia/' . $items->NavLink . '" class="text-decoration-none ms-2 mt-2 d-md-none nav-name"> ' . $items->NavName . '</a> </li><hr>';
            }
          }

          // Bildirim
          $countnoti = $db->getColumnData("SELECT COUNT(*) FROM notifications WHERE MemberID = ? AND NotificationStatus = ?", array($memberid, 1));
          if ($countnoti > 9) {
            $countnoti = "9+";
          }
          $ishaveNoti = $db->getData("SELECT * FROM notifications WHERE MemberID = ? AND NotificationActiveness = ?", array($memberid, 1));
          if ($ishaveNoti) {
            $navAnimate = 'navanimate';
            $Notiicon = '<i class="fas fa-bell iconanimate notificationIcon"></i>';
          } else {
            $Notiicon = '<i class="fas fa-bell notificationIcon"></i>';
            $navAnimate = '';
          }
          echo '<li class="nav-item dropdown d-none d-md-flex my-2 my-md-auto mx-auto mx-md-2 d-flex justify-content-center">
                    <a id="notiDropdown" role="button" data-bs-toggle="dropdown" class="nav-link text-dark rounded-circle text-center ' . $navAnimate . '" style="width:40px;height:40px;background:#f8f9fa;" href="#" onClick="deleteNotis()">' . $Notiicon . '</a>
                    <span class="d-none d-md-block position-absolute top-0 mt-1 start-85 translate-middle p-0 bg-noti border border-light rounded-circle text-light text-center" id="noti_count" style="line-height:30px;width:30px;height:30px;font-size:14px;">' . $countnoti . '</span><hr>';

          ?>
          <ul class="dropdown-menu dropdown-menu-end mt-2" style="width:250px;" id="allNotifications">
            <li class="text-center"><?= $translates["friendreq"] ?></li>
            <hr class="mb-0">
            <div style="max-height:14vh;overflow-y:auto;overflow-x:hidden;" class="friend_requests_noti">
              <?php
              $notifications = $db->getDatas("SELECT * FROM notifications WHERE MemberID = ? ORDER BY NotificationID DESC", array($memberid));
              if (!$notifications) { ?>
                <li style="list-style-type: none;" class="p-1 text-center nonoti"><?= $translates["nonoti"] ?></li>
                <?php } else {
                foreach ($notifications as $notification) {
                  if ($notification->NotificationFrom == 'friends') {
                    $FriendID = $notification->OperationID;
                    $isHaveRequest = $db->getData("SELECT * FROM friends WHERE FriendID = ?", array($FriendID));
                    if ($isHaveRequest) {
                      $friendshipactiveness = $db->getColumnData("SELECT FriendRequest FROM friends WHERE FriendID = ?", array($FriendID));
                      if ($friendshipactiveness == 0) {
                        if ($notification->NotificationActiveness == 1) {
                          $notistyle = "background:Red";
                        } else {
                          $notistyle = "";
                        }
                        $personID = $db->getColumnData("SELECT FirstMemberID FROM friends WHERE FriendID = ?", array($FriendID));
                        $personimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
                        if (is_null($personimg)) {
                          if ($gender == 'Erkek') {
                            $personimg = "profilemale.png";
                          } else {
                            $personimg = "profilefemale.png";
                          }
                        }
                        $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));

                ?>
                        <li class="bg-transparent my-2 pt-1 each_request_<?= $FriendID ?>" <?= $notistyle ?> style="border:none" id="each_noti_<?= $notification->NotificationID ?>">
                          <div class="row justify-content-center align-items-center">
                            <div class="col-2 ps-3"><img src="images_profile/<?= $personimg ?>" class="rounded-circle" width="40" height="40"></div>
                            <div class="col-7">
                              <h6 class="m-0 text-center" style="font-size:14px;"><?= $personNames ?></h6>
                            </div>
                            <div class="col-3 ps-0 text-center">
                              <i class="fas fa-times refuse-request" style="font-size:12px;" onClick="FriendAcceptment('refuse','<?= $personID ?>','<?= $FriendID ?>')"></i>
                              <i class="fas fa-check accept-request" style="font-size:12px;" onClick="FriendAcceptment('accept','<?= $personID ?>','<?= $FriendID ?>')"></i>
                            </div>
                          </div>
                        </li>
              <?php }
                    }
                  }
                }
              } ?>
            </div>
          </ul>
          <hr>
          </li>
          <!-- Bildirim Bitiş    -->
          <li class="w-100 nav-item mt-2 my-md-auto mx-auto mx-md-2 d-flex d-md-none flex-column justify-content-center align-items-center">
            <div class="d-flex flex-row mb-2" id="notifications_mobile" onClick="deleteNotis()">
              <a href="javascript:void(0)" class="nav-link text-dark rounded-circle text-center <?= $navAnimate ?>" style="width:40px;height:40px;background:#f8f9fa;" id="noticon_mb"><?= $Notiicon ?></a>
              <a href="javascript:void(0)" class="text-decoration-none ms-2 mt-2 d-md-none nav-name"> <?= $translates["Notifications"] ?></a>
            </div>
            <ul class="m-0 p-0 w-100 allNotifications" state="closed" style="display:none;" id="noti_box_mb">
              <div style="max-height:15vh;overflow-y:auto;overflow-x:hidden;" class="bg-light rounded-3 p-3 friend_requests_noti">
                <?php
                $firstnoti = $db->getColumnData("SELECT NotificationID FROM notifications WHERE MemberID = ? ORDER BY NotificationID ASC", array($memberid));
                $notifications = $db->getDatas("SELECT * FROM notifications WHERE MemberID = ? ORDER BY NotificationID DESC", array($memberid));
                if (!$notifications) { ?>
                  <li style="list-style-type: none;" class="text-center nonoti"><?= $translates["nonoti"] ?></li>
                  <?php } else {
                  foreach ($notifications as $notification) {
                    if ($notification->NotificationFrom == 'friends') {
                      $FriendID = $notification->OperationID;
                      $isHaveRequest = $db->getData("SELECT * FROM friends WHERE FriendID = ?", array($FriendID));
                      if ($isHaveRequest) {
                        $friendshipactiveness = $db->getColumnData("SELECT FriendRequest FROM friends WHERE FriendID = ?", array($FriendID));
                        if ($friendshipactiveness == 0) {
                          if ($notification->NotificationActiveness == 1) {
                            $notistyle = "background:Red";
                          } else {
                            $notistyle = "";
                          }
                          $personID = $db->getColumnData("SELECT FirstMemberID FROM friends WHERE FriendID = ?", array($FriendID));
                          $personimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
                          if (is_null($personimg)) {
                            if ($gender == 'Erkek') {
                              $personimg = "profilemale.png";
                            } else {
                              $personimg = "profilefemale.png";
                            }
                          }
                          $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));

                  ?>
                          <li class="d-md-none my-2 pt-1 each_request_<?= $FriendID ?>" <?= $notistyle ?> style="border:none;list-style-type:none" id="each_mb_noti_<?= $notification->NotificationID ?>">
                            <div class="row justify-content-center align-items-center">
                              <div class="col-3 m-0 p-0 ps-3"><img src="images_profile/<?= $personimg ?>" class="rounded-circle" width="40" height="40"></div>
                              <div class="col-6 m-0 p-0 d-flex justify-content-center">
                                <h6 class="m-0 text-center" style="font-size:17px;"><?= $personNames ?></h6>
                              </div>
                              <div class="col-3 m-0 p-0 ps-0 text-center">
                                <i class="fas fa-times refuse-request" style="font-size:15px;" onClick="FriendAcceptment('refuse','<?= $personID ?>','<?= $FriendID ?>')"></i>
                                <i class="fas fa-check accept-request" style="font-size:15px;" onClick="FriendAcceptment('accept','<?= $personID ?>','<?= $FriendID ?>')"></i>
                              </div>
                            </div>
                          </li>
                          <?php echo ($firstnoti == $notification->NotificationID) ? "" : "<hr>" ?>
                <?php }
                      }
                    }
                  }
                } ?>
              </div>
            </ul>
          </li>
          <hr>
          <?php $list = $db->getDatas("SELECT * FROM navbar_$language LIMIT 5,3");
          echo '<li class="nav-item my-2 my-md-auto mx-auto mx-md-2 d-md-none d-flex justify-content-center">';
          foreach ($list as $items) {
            if ($page == $items->NavLink) {
              echo '<a class="nav-link text-light rounded-circle text-center ms-3" style="width:40px;height:40px;background:rgba(0, 0, 0, 0.39)" href="http://localhost/aybu/socialmedia/' . $items->NavLink . '">' . $items->NavIcon . '</a><a href="http://localhost/aybu/socialmedia/' . $items->NavLink . '" class="text-decoration-none ms-2 mt-2 d-md-none nav-name text-light">' . $items->NavName . '</a>';
            } else {
              echo '<a class="nav-link text-dark bg-light rounded-circle text-center ms-3" style="width:40px;height:40px" href="http://localhost/aybu/socialmedia/' . $items->NavLink . '">' . $items->NavIcon . '</a><a href="http://localhost/aybu/socialmedia/' . $items->NavLink . '" class="text-decoration-none ms-2 mt-2 d-md-none nav-name"> ' . $items->NavName . '</a>';
            }
          }
          echo '</li><hr>'; ?>
          <li class="nav-item text-center d-md-none">
            <img class="me-1 mb-1 rounded-3 shadow-sm" style="width:35px;height:20px;cursor:pointer" src="images/tr.png" onClick="ChangeLang('tr','<?= $page ?>','<?= $part ?>')" <?php echo ($language == 'tr' ? 'style="opacity:1"' : 'style="opacity:0.7"') ?>>
            <img class="mb-1 rounded-3 shadow-sm" style="width:35px;height:20px;cursor:pointer" src="images/en.png" onClick="ChangeLang('en','<?= $page ?>','<?= $part ?>')" <?php echo ($language == 'en' ? 'style="opacity:1"' : 'style="opacity:0.7"') ?>>
          </li>
          <hr>
          <li class="nav-item text-center d-md-none mb-3 text-wrap text-light"><small><i class="far fa-copyright"></i><?= $translates["copy_right"] ?></small></li>
          <li class="nav-item dropdown text-center d-none d-md-flex">
            <a class="nav-link dropdown-toggle text-light rounded-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="images_profile/<?= $profile_photo; ?>" style="width:40px;height:40px;" class="rounded-circle">
            </a>
            <ul class="dropdown-menu dropdown-menu-end mt-2">
              <li>
                <a class="dropdown-item" href="javascript:void(0)">
                  <?= $user_name . " " . $user_lastname; ?>
                  <img src="images_profile/<?= $profile_photo; ?>" style="width:30px;height:30px;border-radius:25px;margin-right:5%;">
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <?php

              $list = $db->getDatas("SELECT * FROM navbar_$language LIMIT 5,3");

              foreach ($list as $items) {
                echo '<li><a class="dropdown-item" href="http://localhost/aybu/socialmedia/' . $items->NavLink . '"><span> ' . $items->NavIcon . " " . $items->NavName . '</span></a></li>';
              }
              ?>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li class="dropdown-item text-center">
                <img class="me-1 mb-1 rounded-3 shadow-sm" style="width:35px;height:20px;cursor:pointer" src="images/tr.png" onClick="ChangeLang('tr','<?= $page ?>','<?= $part ?>','<?=$edit?>')" <?php echo ($language == 'tr' ? 'style="opacity:1"' : 'style="opacity:0.7"') ?>>
                <img class="mb-1 rounded-3 shadow-sm" style="width:35px;height:20px;cursor:pointer" src="images/en.png" onClick="ChangeLang('en','<?= $page ?>','<?= $part ?>','<?=$edit?>')" <?php echo ($language == 'en' ? 'style="opacity:1"' : 'style="opacity:0.7"') ?>>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li class="dropdown-item text-wrap"><small><i class="far fa-copyright"></i><?= $translates["copy_right"] ?></small></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="d-none d-md-block row mx-auto fixed-top justify-content-center" style="width:900px;margin-top: 86px;">
    <div class="list-group col-6 mx-auto" id="search_result">

    </div>
  </div>
  <div style="height:12vh;"></div>