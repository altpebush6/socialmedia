<?php
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Europe/Istanbul');

require_once "functions/time.php";
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";

if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) or strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != 'xmlhttprequest') {
    header("Location: http://localhost/aybu/socialmedia/404.php");
}

$db = new aybu\db\mysqlDB();
$SS = new aybu\session\session();

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}
require_once "languages/language_" . $language . ".php";

$operation = $_GET['operation'];
$memberid = $SS->get("MemberID");

$result = array();

switch ($operation) {
    case 'confessit':
        $text = security("Text");
        $visibility = security("Visibility");
        $topic = security("Topic");
        $db->Insert("INSERT INTO confessions SET MemberID = ?,
                                                ConfessionText = ?,
                                                ConfessionTopic = ?,
                                                ConfessionVisibility = ?", array($memberid, $text, $topic, $visibility));
        echo json_encode($result);
        break;

    case 'editconfession':
        $CnfnID = security("CnfnID");
        $text = security("Text");
        $visibility = security("Visibility");
        $topic = security("Topic");
        $db->Update("UPDATE confessions SET ConfessionText = ?,
                                            ConfessionTopic = ?,
                                            ConfessionVisibility = ? WHERE ConfessionID = ? ", array($text, $topic, $visibility, $CnfnID));
        echo json_encode($result);
        break;
}
