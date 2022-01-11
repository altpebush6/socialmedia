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
// Dili çek
require_once "languages/language_" . $language . ".php";

$memberid = $SS->get("MemberID");
$operation = $_GET["operation"];

$result = array();

switch ($operation) {
    case 'setNoti':
        $getNoti = $db->getData("SELECT * FROM notifications WHERE MemberID = ? AND NotificationActiveness = ?", array($memberid, 1));
        if ($getNoti) {
            $notifications = $db->getDatas("SELECT * FROM notifications WHERE MemberID = ? AND NotiHasShown = ?", array($memberid, 0));
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
                            $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
                            if (is_null($personimg)) {
                                if ($gender == 'Male') {
                                    $personimg = "profilemale.png";
                                } else {
                                    $personimg = "profilefemale.png";
                                }
                            }
                            $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
                            $result["data"] = '<li class="bg-transparent my-2 each_request_' . $FriendID . '" ' . $notistyle . ' style="border:none;list-style-type:none;" id="each_noti_' . $notification->NotificationID . '">
                                            <div class="row justify-content-center align-items-center">
                                                <div class="col-2 ps-3"><img src="images_profile/' . $personimg . '" class="rounded-circle" width="40" height="40"></div>
                                                <div class="col-7">
                                                <h6 class="m-0 text-center" style="font-size:14px;">' . $personNames . '</h6>
                                                </div>
                                                <div class="col-3 ps-0 text-center">
                                                <i class="fas fa-times refuse-request" style="font-size:12px;" onClick=\'FriendAcceptment("refuse", "' . $personID . '","' . $FriendID . '")\'></i>
                                                <i class="fas fa-check accept-request" style="font-size:12px;" onClick=\'FriendAcceptment("accept", "' . $personID . '","' . $FriendID . '")\'></i>
                                                </div>
                                            </div>
                                        </li>';

                            $setShown = $db->Update("UPDATE notifications SET NotiHasShown = ? WHERE MemberID = ?", array(1, $memberid));
                        }
                    }
                }
            }
            $result["what"] = "true";
        }

        $deletednoti = $db->getData("SELECT * FROM notifications WHERE NotificationStatus = ? AND MemberID = ? ORDER BY NotificationID DESC", array(0, $memberid));
        if ($deletednoti) {
            $delnotiID = $deletednoti->NotificationID;
            $result["deletednotiID"] = $delnotiID;
        }

        $newCountNoti = $db->getColumnData("SELECT COUNT(*) FROM notifications WHERE MemberID = ? AND NotificationStatus = ? AND NotiHasShown = ?", array($memberid, 1, 1));
        if ($newCountNoti > 9) {
            $newCountNoti = "9+";
        }
        $result["newCountNoti"] = $newCountNoti;
        echo json_encode($result);
        break;
    case 'deleteNotifications':
        $del = $db->Update("UPDATE notifications SET NotificationActiveness = ? WHERE MemberID = ?", array(0, $memberid));
        break;
}
