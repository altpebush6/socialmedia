<?php
if (!isset($_SESSION)) {
    session_start();
  }
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";

if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) or strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != 'xmlhttprequest') {
    header("Location: http://localhost/aybu/socialmedia/404.php");
}
$db = new aybu\db\mysqlDB();
$SS = new aybu\session\session();

$memberid = security("MemberID");

$memberconfirm = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?",array($memberid));
if ($memberconfirm != 1) {
    $SS->create("LogedIn", false);
    $SS->del("MemberID");
    $result = "problem";
} else {
    $result = "ok";
}

echo $result;
