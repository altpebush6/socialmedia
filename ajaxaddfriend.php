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
$friendid = security("UserID");
$operation = $_GET["operation"];

$result = array();

switch ($operation) {
  case 'add':
    $addFriend = $db->Insert("INSERT INTO friends SET FirstMemberID = ?, SecondMemberID = ?", array($memberid, $friendid));
    $result["success"] = "<button id='RequestFriendButton' onClick=\"FriendButton('request','" . $friendid . "')\"><i class='fas fa-user-check'></i> Arkadaşlık isteği gönderildi</button>";
    $addNotification = $db->Insert("INSERT INTO notifications SET MemberID = ?, NotificationFrom = ?, OperationID = ?",array($friendid,'friends',$addFriend));
    echo json_encode($result);
    break;

  case 'remove':
    $operationID = $db->getColumnData("SELECT FriendID FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ?", array($memberid, $friendid));
    $removeFriend = $db->Delete("DELETE FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ?", array($memberid, $friendid));
    $removeFriend = $db->Delete("DELETE FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ?", array($friendid, $memberid));
    $result["success"] = "<button id='addFriendButton' onClick=\"FriendButton('add','" . $friendid . "')\"><i class='fas fa-user-plus'></i> Arkadaş Ekle</button>";
    $deletenoti = $db->Delete("DELETE FROM notifications WHERE OperationID = ?", array($operationID));
    echo json_encode($result);
    break;

  case 'requestAccept':
    $acceptRequest = $db->Update("UPDATE friends SET FriendRequest = ? WHERE FirstMemberID = ? AND SecondMemberID = ?", array(1, $friendid, $memberid));
    $acceptRequest = $db->getColumnData("SELECT FriendID FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ?", array($friendid, $memberid));
    $result["success"] = "<button id='RemoveFriendButton' onClick=\"FriendButton('remove','" . $friendid . "')\"><i class='fas fa-user-check'></i> Arkadaşsınız</button>";
    $addNotification = $db->Insert("INSERT INTO notifications SET MemberID = ?, NotificationFrom = ?, OperationID = ?",array($friendid,'friends',$acceptRequest));
    $deletenoti = $db->Delete("DELETE FROM notifications WHERE MemberID = ? AND OperationID = ?", array($memberid,$addNotification));
    echo json_encode($result);
    break;

  case 'accept':
    $FriendID = security("FriendID");
    $operationID = $db->getColumnData("SELECT FriendID FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ?", array($friendid, $memberid));
    $addNotification = $db->Update("UPDATE notifications SET NotificationStatus = ? AND NotificationActiveness = ? WHERE OperationID = ?",array(0,0,$operationID));
    $accept = $db->Update("UPDATE friends SET FriendRequest = ? WHERE FriendID = ?", array(1, $FriendID));
    $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($friendid));  
    $personimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($friendid));
    $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($friendid));
    if (is_null($personimg)) {
      if ($gender == 'Erkek') {
        $personimg = "profilefullmale.jpg";
      } else {
        $personimg = "profilefullfemale.jpg";
      }
    }
    $result["success"] = '<li class="list-group-item bg-transparent mb-3 py-3">
                              <div class="row align-items-center justify-content-center">
                                  <div class="col-2 text-center"><img src="images_profile/' . $personimg . '" class="rounded-circle" width="50" height="50"></div>
                                  <div class="col-7 text-light text-start">
                                      <h4>' . $personNames . '</h4>
                                  </div>
                                  <div class="col-3 p-0"><a class="btn btn-outline-light" href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $friendid . '">' . $translates["goprofile"] . '</a></div>
                              </div>';

    $isHave = $db->getDatas("SELECT * FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($memberid, 0));

    $request_count = $db->getColumnData("SELECT COUNT(*) FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($memberid, 0));
    $friend_count = $db->getColumnData("SELECT COUNT(*) FROM friends WHERE (FirstMemberID = ? OR SecondMemberID = ?) AND FriendRequest = ?", array($memberid,$memberid, 1));

    $result["friend_count"] = $friend_count;

    if ($isHave) {
      $result["norequest"] = 'null';
      $result["request_count"] = $request_count;
    } else {
      $result["norequest"] = '<h4 class="m-0">' . $translates["friendrequests"] ." ". '<span class="badge bg-primary friend_request_count">' . $request_count . '</span><li class="list-group-item bg-transparent" style="padding:5%;font-size:19px;color:white;border:none">' . $translates["norequest"] .'</h4></li>';
    }
    $countnoti = $db->getColumnData("SELECT COUNT(*) FROM notifications WHERE MemberID = ? AND NotificationStatus = ?",array($memberid,1));
    $result["countnoti"] = $countnoti;
    $deletenoti = $db->Delete("DELETE FROM notifications WHERE MemberID = ? AND OperationID = ?", array($memberid,$operationID));
    echo json_encode($result);
    break;

  case 'refuse':
    $FriendID = security("FriendID");
    $operationID = $db->getColumnData("SELECT FriendID FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ?", array($friendid, $memberid));
    $addNotification = $db->Update("UPDATE notifications SET NotificationStatus = ? AND NotificationActiveness = ? WHERE OperationID = ?",array(0,0,$operationID));
    $refuse = $db->Delete("DELETE FROM friends WHERE FriendID = ?", array($FriendID));
    $isHave = $db->getDatas("SELECT * FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($memberid, 0));
    $request_count = $db->getColumnData("SELECT COUNT(*) FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($memberid, 0));
    if ($isHave) {
      $result["norequest"] = 'null';
      $result["request_count"] = $request_count;
    } else {
      $result["norequest"] = '<h4 class="m-0">' . $translates["friendrequests"] ." ". '<span class="badge bg-primary friend_request_count">' . $friend_count . '</span><li class="list-group-item bg-transparent" style="padding:5%;font-size:19px;color:white;border:none">' . $translates["norequest"] . '</h4></li></h4>';
    }
    $countnoti = $db->getColumnData("SELECT COUNT(*) FROM notifications WHERE MemberID = ? AND NotificationStatus = ?",array($memberid,1));
    $result["countnoti"] = $countnoti;
    $deletenoti = $db->Delete("DELETE FROM notifications WHERE MemberID = ? AND OperationID = ?", array($memberid,$operationID));
    echo json_encode($result);
    break;
}
