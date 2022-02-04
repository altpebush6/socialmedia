<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once "functions/routing.php";
require_once "classes/AllClasses.php";
require_once "functions/getmonth.php";
require_once "functions/security.php";

if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) or strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != 'xmlhttprequest') {
    header("Location: http://localhost/aybu/socialmedia/404.php");
}
$SS = new aybu\session\session();

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}
require_once "languages/language_" . $language . ".php";

$result = array();

$nextWeek = strtotime("7 February 2022");
$now = time();
$diff = $nextWeek - $now;

$day = intval($diff / 86400);
$diff = $diff % 86400;

$hour = intval($diff / 3600);
$diff = $diff % 3600;

$min = intval($diff / 60);
$diff = $diff % 60;

echo $day . " Gün : " . $hour . " Saat : " . $min . " Dakika : " . $diff . " Saniye";
