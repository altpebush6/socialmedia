<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "classes/AllClasses.php";
require_once "functions/routing.php";

$SS = new aybu\session\session();
$db = new aybu\db\mysqlDB();

$MemberID = $SS->get("MemberID");
$AdminID = $SS->get("AdminID");

if ($MemberID) {
  $db->Update("UPDATE members SET MemberStatus = ? WHERE MemberID = ?", array(0, $MemberID));
  $SS->create("LogedIn", false);
  $SS->del("MemberID");
} else {
  $SS->create("LogedIn", false);
  $SS->del("AdminID");

  $TimeID = $SS->get("TimeID");
  $nowtime = date("d-m-Y H:i:s");
  $db->Update("UPDATE admintimes SET LogoutTime = ? WHERE TimeID = ?", array($nowtime, $TimeID));
}

if ($SS->isHave("Language")) {
  $language = $SS->get("Language");
} else {
  $language = "tr";
}
$gopage = ($language == 'en' ? 'login' : 'giris');
go("http://localhost/aybu/socialmedia/" . $gopage . "");
