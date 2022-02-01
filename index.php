<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once "classes/AllClasses.php";

$SS = new aybu\session\session();
$page = $_GET['page'];

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}

define("aybupages.?", true);

if (!defined("aybupages.?")) {
    header("Location: http://localhost/aybu/404.php");
}

if ($SS->get("MemberID")) {
    switch ($page) {
        case '':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/anasayfa");
            } else {
                header("Location: http://localhost/aybu/socialmedia/home");
            }
            break;

        case 'login':
        case 'giris':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/anasayfa");
            } else {
                header("Location: http://localhost/aybu/socialmedia/home");
            }
            break;

        case 'home':
        case 'anasayfa':
            require_once "header.php";
            require_once "homeindex.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'messages':
        case 'mesajlar':
            require_once "header.php";
            require_once "messengerindex.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'clubs':
        case 'kulupler':
            require_once "header.php";
            require_once "clubsindex.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'events':
        case 'etkinlikler':
            require_once "header.php";
            require_once "eventsindex.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'courses':
        case 'dersler':
            require_once "header.php";
            require_once "coursesindex.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'confessions':
        case 'itiraflar':
            require_once "header.php";
            require_once "confessionsindex.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'profile':
        case 'profil':
            require_once "header.php";
            require_once "profileindex.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'settings':
        case 'ayarlar':
            require_once "header.php";
            require_once "settingsindex.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'forgotpassword':
        case 'sifremiunuttum':
            require_once "header.php";
            require_once "forgotpass.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'arkadasara':
        case 'findfriend':
            require_once "header.php";
            require_once "searchfriend.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'friends':
        case 'arkadaslar':
            require_once "header.php";
            require_once "friendsindex.php";
            require_once "functions/ajaxfunctions.php";
            break;

        case 'news':
        case 'haberler':
            require_once "header.php";
            require_once "newsindex.php";
            require_once "functions/ajaxfunctions.php";
            break;
        default:
            header("Location: http://localhost/aybu/socialmedia/404.php");
            break;
    }
} else {
    switch ($page) {
        case 'adminpaneli':
            if ($SS->get("AdminID")) {
                require_once "mainindex.php";
            } else {
                header("Location: http://localhost/aybu/socialmedia/404.php");
            }
            break;

        case 'login':
        case 'giris':
            if ($SS->get("AdminID")) {
                header("Location: http://localhost/aybu/socialmedia/adminpaneli");
            } else {
                require_once "loginindex.php";
            }
            break;

        case 'forgotpassword':
        case 'sifremiunuttum':
            require_once "forgotpass.php";
            break;

        case '':
            if ($SS->get("AdminID")) {
                header("Location: http://localhost/aybu/socialmedia/adminpaneli");
            } else {
                if ($language == 'tr') {
                    header("Location: http://localhost/aybu/socialmedia/giris");
                } else {
                    header("Location: http://localhost/aybu/socialmedia/login");
                }
            }
            break;

        case 'home':
        case 'anasayfa':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;

        case 'messages':
        case 'mesajlar':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;

        case 'clubs':
        case 'kulupler':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;

        case 'profile':
        case 'profil':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;

        case 'settings':
        case 'ayarlar':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;

        case 'searchfriend':
        case 'arkadasara':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;

        case 'friends':
        case 'arkadaslar':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;

        case 'events':
        case 'etkinlikler':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;
        case 'courses':
        case 'kurslar':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;
        case 'confessions':
        case 'itiraflar':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;
            break;

        case 'news':
        case 'haberler':
            if ($language == 'tr') {
                header("Location: http://localhost/aybu/socialmedia/giris");
            } else {
                header("Location: http://localhost/aybu/socialmedia/login");
            }
            break;
        default:
            header("Location: http://localhost/aybu/socialmedia/404.php");
            break;
    }
}

?>
</body>

</html>