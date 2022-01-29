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

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}
require_once "languages/language_" . $language . ".php";

$memberid = $SS->get("MemberID");

$operation = $_GET['operation'];
$result = array();
$allIDs = array();
$allmembers = $db->getDatas("SELECT * FROM members WHERE MemberConfirm = ?", array(1));
foreach ($allmembers as $member) {
    array_push($allIDs, $member->MemberID);
}


$db->TableOperations("TRUNCATE TABLE matching");
while (count($allIDs) > 1) {
    $pairs = array_rand($allIDs, 2);
    $firstMemberID = $allIDs[$pairs[0]];
    $secondMemberID = $allIDs[$pairs[1]];
    $db->Insert("INSERT INTO matching SET FirstMemberID = ?, SecondMemberID = ?", array($firstMemberID, $secondMemberID));
    unset($allIDs[$pairs[0]]);
    unset($allIDs[$pairs[1]]);
}
$result["array"] = $matchedpeople;
echo json_encode($result);
