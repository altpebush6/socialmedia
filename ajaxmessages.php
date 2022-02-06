<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";
require_once "functions/time.php";
date_default_timezone_set('Europe/Istanbul');

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

$result = array();

$memberid = $SS->get("MemberID");
$operation = $_GET["operation"];

$user_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($memberid));
$user_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = ?", array($memberid));
$usernames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($memberid));
$member_profile = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberid));
$gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($memberid));
if (is_null($member_profile)) {
  if ($gender == 'Male') {
    $member_profile = "profilemale.png";
  } else {
    $member_profile = "profilefemale.png";
  }
}

switch ($operation) {
  case 'showmessages':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $personID = security("personID");
      if ($personID != $translates["group"]) {
        // PERSONAL MESSAGES
        $profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberid));
        $lastMessageID = security("lastid");
        if (empty($lastMessageID)) {
          $lastMessageID = 0;
        }

        $messages = $db->getDatas("SELECT * FROM messages
                                       WHERE MessageStatus = 1 AND MessageID > $lastMessageID AND (MessageFromID = ? AND MessageToID = ?)", array($personID, $memberid));
        foreach ($messages as $item) {
          $hasSeen = $items->MessageHasSeen;
          if ($hasSeen == 1) {
            $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
          } else {
            $tic = '<i class="fas fa-check" style="font-size:13px;"></i>';
          }
          $getprofileimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
          $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
          $isPersonActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($personID));
          if ($isPersonActive != 1) {
            $getprofileimg = NULL;
          }
          if (is_null($getprofileimg)) {
            if ($gender == 'Male') {
              $getprofileimg = "profilemale.png";
            } else {
              $getprofileimg = "profilefemale.png";
            }
          }
          $msgName = $item->MessageImg;

          if ($msgName) {
            $messageimage = '<img src="message_images/' . $msgName . '" class="rounded-2" style="width:250px;min-height:20vh;">';
            $result["message"] = '<li class="list-group-item bg-transparent p-4 py-1" style="border:none;" id="each_message_' . $item->MessageID . '" lastid="' . $item->MessageID . '">
                                    <div class="row">   
                                      <div class="col-2 p-0 col-lg-1 text-center me-lg-3">
                                      <a href="http://localhost/aybu/socialmedia/' . $translates["profile"] . '/' . $personID . '"><img src="images_profile/' . $getprofileimg . '" class="rounded-circle shadow-lg" width="60" height="60"></a>
                                      </div>      
                                      <div class="col-10 d-flex justify-content-start p-0 text-start message-content-img">
                                        <a class="w-33" href="message_images/' . $msgName . '">' . $messageimage . '</a>
                                        <span class="time-img text-light fs-6 m-2 p-1 align-self-end rounded-2 position-absolute" style="font-size: 13px !important;">
                                        ' . messageTime($item->MessageAddTime) . '
                                        </span>
                                      </div>
                                      <script>baguetteBox.run(\'.message-content-img\');</script>
                                    </div>
                                  </li>';
          } else {
            $result["message"] = '<li class="list-group-item bg-transparent p-4 py-1" style="border:none;" id="each_message_' . $item->MessageID . '" lastid="' . $item->MessageID . '">
                                  <div class="row">     
                                    <div class="col-2 col-lg-1 text-center p-0 me-2 me-md-3 me-lg-4">
                                    <a href="http://localhost/aybu/socialmedia/' . $translates["profile"] . '/' . $personID . '"><img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60"></a>
                                    </div>        
                                    <div class="col-2 p-0" style="width:auto;max-width:250px;min-width:75px;">
                                      <div class="row align-items-center bg-light text-dark rounded-3 shadow" style="height:100%;max-width:200px;">
                                        <div class="p-0 w-100" style="width:auto;max-width:200px;">
                                          <p class="m-0 py-1 px-2 fs-6 text-break">' . $item->MessageText . '</p>
                                        </div>
                                        <div class="bg-light text-dark text-end px-2 rounded-3" style="border-top-left-radius:0px !important;border-top-right-radius:0px !important;font-size:12px">
                                          ' . messageTime($item->MessageAddTime) . '
                                        </div>
                                      </div>
                                    </div>
                                  </div>   
                                </li>';
          }
        }
        $ishave = $db->getData("SELECT * FROM messages WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))", array(1, $memberid, $personID, $personID, $memberid));
        $name_lastname = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
        $MemberName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($personID));

        $newmessageID = $db->GetColumnData("SELECT MessageID FROM messages
                                                WHERE MessageStatus = 1 AND ((MessageFromID = $memberid AND MessageToID = $personID) OR (MessageFromID = $personID AND MessageToID = $memberid))
                                                ORDER BY MessageAddTime DESC");
        $messageText = $db->getColumnData("SELECT MessageText FROM messages WHERE MessageID = ?", array($newmessageID));
        $messageImg = $db->getColumnData("SELECT MessageImg FROM messages WHERE MessageID = ?", array($newmessageID));

        if ($messageImg) {
          $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
        }

        $whosemessage =  $db->GetColumnData("SELECT MessageFromID FROM messages WHERE MessageID = ?", array($newmessageID));

        $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages
                                                WHERE MessageID = ?", array($newmessageID));

        if ($whosemessage == $memberid) {
          $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($messageID));
          if ($messageHasSeen == 1) {
            $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
          } else {
            $tic = '<i class="fas fa-check" style="font-size:13px;"></i>';
          }
        } else {
          $tic = '';
        }

        $result["personID"] = $personID;

        $content = ($whosemessage == $memberid  ? $translates["you"] : $MemberName) . ": " . $messageText . " " . $tic;

        $time = $db->getColumnData("SELECT MemberTime FROM members WHERE MemberID = ?", array($personID));
        $now_time = date("Y-m-d H:i:s");
        $strt = strtotime($time);
        $fnsh = strtotime($now_time);
        $diff = abs($fnsh - $strt);
        if ($diff < 10) {
          $styleicon = "style='color:green'";
        } else {
          $styleicon = "style='color:rgb(204, 1, 1)'";
        }

        $resultcontent = $MemberName . ": " . $messageText;

        if (!$ishave) {
          $result["nonconversation"] = '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '" style=\'background:rgba(255, 255, 255, 0.2)\'>
                                        <div class="row my-2 justify-content-center align-items-center">
                                          <div class="col-2 text-center">
                                            <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                          </div>
                                          <div class="col-8 px-3 ps-md-5 ps-lg-4 ps-xl-5 ">
                                            <div class="row fs-5">' . $name_lastname . '</div>
                                            <div class="row">
                                              <div class="col-12 p-0 text-start person-content" id="content_' . $personID . '" style="opacity:0.5">
                                              ' . $resultcontent . '
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-2 text-center d-flex flex-column justify-content-between">
                                            <div class="row"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                            <div class="row" id="chatpersontime_' . $personID . '"><small>' . messageTime($messagetime) . '</small></div>
                                          </div>
                                        </div>
                                      <hr class="my-3"></A>';
        } else {
          $result["conversationtrue"] = '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '" style=\'background:rgba(255, 255, 255, 0.2)\'>
                                        <div class="row my-2 justify-content-center align-items-center">
                                          <div class="col-2 text-center">
                                            <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                          </div>
                                          <div class="col-8 px-3 ps-md-5 ps-lg-4 ps-xl-5 ">
                                            <div class="row fs-5">' . $name_lastname . '</div>
                                            <div class="row">
                                              <div class="col-12 p-0 text-start person-content" id="content_' . $personID . '" style="opacity:0.5">
                                              ' . $resultcontent . '
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-2 text-center d-flex flex-column justify-content-between">
                                            <div class="row"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                            <div class="row" id="chatpersontime_' . $personID . '"><small>' . messageTime($messagetime) . '</small></div>
                                          </div>
                                        </div>
                                      <hr class="my-3"></A>';
        }
        $sawMessage = $db->Update("UPDATE messages SET MessageHasSeen = ? WHERE MessageFromID = ? AND MessageToID = ?", array(1, $personID, $memberid));
        $readMessage = $db->Update("UPDATE chatbox SET MessageHasRead = ? WHERE MessageFromID = ? AND MessageToID = ?", array(1, $personID, $memberid));
        $readMessage2 = $db->Update("UPDATE messages SET MessageHasRead = ? WHERE MessageFromID = ? AND MessageToID = ?", array(1, $personID, $memberid));

        $isSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageFromID = $memberid AND MessageToID = $personID AND MessageStatus = 1 ORDER BY MessageID DESC");
        if ($isSeen == 1) {
          $result["seen"] = '<i class="fas fa-check-double" style="font-size:12px;color:blue"></i>';
        }
        $deletedmessages = $db->getColumnData("SELECT MessageID FROM messages WHERE MessageStatus = ? AND MessageFromID = ? AND MessageToID = ? ORDER BY MessageID DESC", array(0, $personID, $memberid));
        if ($deletedmessages) {
          $result["deletedmsg"] = $deletedmessages;
          $result["personID"] = $personID;
          $lastmessage = $db->getData("SELECT * FROM messages 
                                    WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))
                                    ORDER BY MessageID DESC", array(1, $memberid, $personID, $personID, $memberid));
          $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages WHERE MessageID = ?", array($lastmessage->MessageID));
          $messagetime = "<small>" . messageTime($messagetime) . "</small>";
          $result["messagetime"] = $messagetime;
          if ($lastmessage->MessageFromID == $memberid) {
            $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($lastmessage->MessageID));
            if ($messageHasSeen == 1) {
              $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
            } else {
              $tic = '<i class="fas fa-check" style="font-size:13px;"></i>';
            }
            $messageText = $lastmessage->MessageText;
            $lastmsgimg = $lastmessage->MessageImg;
            if ($lastmsgimg) {
              $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
            }
            $result["lastcontent"] = $translates["you"] . ": " . $messageText . " " . $tic;
          } else {
            $MemberName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($personID));
            $result["lastcontent"] = $MemberName . ": " . $messageText;
          }
          $anyMessageLeft = $db->getColumnData("SELECT MessageID FROM messages WHERE MessageStatus = 1 AND ((MessageFromID = $memberid AND MessageToID = $personID) OR (MessageFromID = $personID AND MessageToID = $memberid))");
          if (!$anyMessageLeft) {
            $db->Update("UPDATE chatbox SET MessageStatus = ? WHERE (MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?)", array(0, $memberid, $personID, $personID, $memberid));
            $result["nomsg"] = "no message left";
          }
        }
      } else {
        // GROUP MESSAGES
        $lastMessageID = security("lastid");
        if (empty($lastMessageID)) {
          $lastMessageID = 0;
        }
        $GroupID = security("GroupID");

        $messages = $db->getDatas("SELECT * FROM messages_group
                                     WHERE MessageStatus = ? AND MessageID > $lastMessageID AND GroupID = ?", array(1, $GroupID));


        foreach ($messages as $item) {
          $MessageFromID = $item->MessageFromID;
          $Groupimage = $db->getColumnData("SELECT GroupImage FROM all_groups WHERE GroupID = ?", array($GroupID));
          $GroupName = $db->getColumnData("SELECT GroupName FROM all_groups WHERE GroupID = ?", array($GroupID));
          if (is_null($Groupimage)) {
            $Groupimage = "noneimage.png";
          }

          $getprofileimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($MessageFromID));
          $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($MessageFromID));

          $isPersonActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($MessageFromID));
          if ($isPersonActive != 1) {
            $getprofileimg = NULL;
            $personNames = $translates["unknownuser"];
          }
          $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($MessageFromID));
          if (is_null($getprofileimg)) {
            if ($gender == 'Male') {
              $getprofileimg = "profilemale.png";
            } else {
              $getprofileimg = "profilefemale.png";
            }
          }

          $msgImage = $item->MessageImg;

          if ($msgImage) {
            $result["message"] = '<li class="list-group-item bg-transparent p-4 py-1 my-2" style="border:none;" id="each_message_' . $item->MessageID . '" lastid="' . $item->MessageID . '">
                                  <div class="row">   
                                    <div class="col-2 p-0 col-lg-1 text-center me-lg-3">
                                    <a href="http://localhost/aybu/socialmedia/' . $translates["profile"] . '/' . $MessageFromID . '"><img src="images_profile/' . $getprofileimg . '" class="rounded-circle shadow-lg" width="60" height="60"></a>
                                    </div>      
                                    <div class="col-10 d-flex justify-content-start p-0 text-start message-content-img">
                                      <a class="w-33" href="message_images/' . $msgImage . '">
                                      <div class="position-absolute bg-dark text-light m-1 p-1 rounded-2" style="font-size:13px;">' . $personNames . '</div>
                                        <img src="message_images/' . $msgImage . '" class="rounded-2" style="width:250px;min-height:20vh;">
                                      </a>
                                      <span class="time-img text-light fs-6 m-2 p-1 align-self-end rounded-2 position-absolute" style="font-size: 13px !important;">
                                      ' . messageTime($item->MessageAddTime) . '
                                      </span>
                                    </div>
                                    <script>baguetteBox.run(\'.message-content-img\');</script>
                                  </div>
                                </li>';
          } else {
            $result["message"] = '<li class="list-group-item bg-transparent p-4 py-1 my-2" style="border:none;" id="each_message_' . $item->MessageID . '" lastid="' . $item->MessageID . '">
                                <div class="row">     
                                  <div class="col-2 col-lg-1 text-center p-0 me-2 me-md-3 me-lg-4">
                                  <a href="http://localhost/aybu/socialmedia/' . $translates["profile"] . '/' . $MessageFromID . '"><img src="images_profile/' . $getprofileimg . '" class="rounded-circle shadow-lg" width="60" height="60"></a>
                                  </div>        
                                  <div class="col-2 p-0" style="width:auto;max-width:250px;min-width:75px;">
                                    <div class="row align-items-center shadow bg-light text-dark rounded-3" style="height:100%;max-width:200px;">
                                      <div class="p-0 w-100" style="width:auto;max-width:200px;">
                                        <p class="m-0 px-2 text-break bg-dark text-light border-bottom" style="font-size:13px;padding-bottom:1px;padding-top:1px;border-top-left-radius:0.3rem;border-top-right-radius:0.3rem;">' . $personNames . '</p>
                                        <p class="m-0 py-1 px-2 fs-6 text-break">' . $item->MessageText . '</p>
                                      </div>
                                      <div class="bg-light text-dark text-end px-2 rounded-3" style="border-top-left-radius:0px !important;border-top-right-radius:0px !important;font-size:12px">
                                        ' . messageTime($item->MessageAddTime) . '
                                      </div>
                                    </div>
                                  </div>
                                </div>   
                              </li>';
          }
        }

        $sawMessage = $db->Update("UPDATE messages_group SET MessageHasSeen = ? WHERE MessageID = ?", array($memberid . ":", $item->messageID));
        $messageText = $item->MessageText;
        $messageImg = $item->MessageImg;
        if ($messageImg) {
          $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
        }

        $messagetime = $item->MessageAddTime;

        $result["personID"] = $GroupID;

        $content = ($MessageFromID == $memberid  ? $translates["you"] : $personNames) . ": " . $item->MessageText;

        $result["conversationtrue"] = '<a class="text-dark text-decoration-none" id="person_' . $GroupID . '" href=\'http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $translates["group"] . '/' . $GroupID . '\'>
                                      <div class="row my-2 justify-content-center align-items-center">
                                        <div class="col-2 text-center">
                                          <img src="group_images/' . $Groupimage . '" class="rounded-circle" width="60" height="60">
                                        </div>
                                        <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                          <div class="row fs-5">
                                            <div class="col-12 p-0 messenger-names" id="chatbox_name_' . $GroupID . '">
                                              <i class="fas fa-users" style="font-size: 17px;"></i> ' . $GroupName . '
                                            </div>
                                          </div>
                                          <div class="row">
                                            <div class="col-9 p-0 text-start person-content" id="content_' . $GroupID . '" style="opacity:0.5">
                                              ' . $content . '
                                            </div>
                                            <div class="col-3 pe-1 text-end">
                                              <small> ' . messageTime($messagetime) . '</small>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    <hr class="my-3"></A>';

        $isreadmessage = $db->getColumnData("SELECT MessageHasRead FROM chatbox WHERE GroupID = ?", array($GroupID));
        $isreadmessage_array = explode(":", $isreadmessage);
        if (!in_array($memberid, $isreadmessage_array)) {
          $newVal = $isreadmessage . $memberid . ":";
          $readMessage = $db->Update("UPDATE chatbox SET MessageHasRead = ? WHERE GroupID = ?", array($newVal, $GroupID));
        }

      }
    }
    echo json_encode($result);
    break;

  case 'getmessage':
    $partID = security("partID");

    $isDeleted = $db->getDatas("SELECT * FROM messages WHERE MessageStatus = ? AND MessageToID = ? AND MessageHasSeen = ? AND MessageHasRead = ? AND MessageHasShown = ?
                                ORDER BY MessageID DESC", array(0, $memberid, 0, 0, 1));

    foreach ($isDeleted as $isDeleted) {
      $result["deleted"] = $isDeleted;
      if ($isDeleted) {
        $setShown2 = $db->Update("UPDATE messages SET MessageHasShown = ? WHERE MessageID = ?", array(2, $isDeleted->MessageID));
        $result["deleted"] = $isDeleted;
        $personID = $isDeleted->MessageFromID;
        $result["personID"] = $personID;
        $lastmessage = $db->getData("SELECT * FROM messages 
                                    WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))
                                    ORDER BY MessageID DESC", array(1, $memberid, $personID, $personID, $memberid));
        $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages WHERE MessageID = ?", array($lastmessage->MessageID));
        $messagetime = messageTime($messagetime);
        $result["messagetime"] = $messagetime;

        $messageText = $lastmessage->MessageText;
        $lastmsgimg = $lastmessage->MessageImg;
        if ($lastmsgimg) {
          $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
        }
        $isSeen = $lastmessage->MessageHasRead;
        $FromID = $lastmessage->MessageFromID;
        if ($isSeen == 1 or $memberid == $FromID) {
          $result["opacity"] = 0.7;
        }
        if ($lastmessage->MessageFromID == $memberid) {
          $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($lastmessage->MessageID));
          if ($messageHasSeen == 1) {
            $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
          } else {
            $tic = '<i class="fas fa-check" style="font-size:13px;"></i>';
          }
          $result["lastcontent"] = $translates["you"] . ": " . $messageText . " " . $tic;
        } else {
          $MemberName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($personID));
          $result["lastcontent"] = $MemberName . ": " . $messageText;
        }
        $anyMessageLeft = $db->getColumnData("SELECT MessageID FROM messages WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))", array(1, $memberid, $personID, $personID, $memberid));
        if (!$anyMessageLeft) {
          $db->Update("UPDATE chatbox SET MessageStatus = ? WHERE (MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?)", array(0, $memberid, $personID, $personID, $memberid));
          $result["nomsg"] = "no message left";
        }
      }
    }

    $newMessages = $db->getData("SELECT * FROM messages WHERE MessageToID = ? AND MessageStatus = ? AND MessageHasRead = ? AND MessageHasShown = ? ORDER BY MessageID DESC", array($memberid, 1, 0, 0));
    if ($newMessages) {
      $result["newMessages"] = "true";
      $newmessageID = $newMessages->MessageID;
      $personID = $newMessages->MessageFromID;
      $messageText = $newMessages->MessageText;
      $messageImg = $newMessages->MessageImg;
      $messagetime = $newMessages->MessageAddTime;
      $name_lastname = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
      $MemberName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($personID));
      $isPersonActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($personID));
      if ($isPersonActive != 1) {
        $getprofileimg = NULL;
        $MemberName = $translates["unknownuser"];
      }
      $getprofileimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
      $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
      if (is_null($getprofileimg)) {
        if ($gender == 'Male') {
          $getprofileimg = "profilemale.png";
        } else {
          $getprofileimg = "profilefemale.png";
        }
      }
      $ishave = $db->getData("SELECT * FROM messages WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))", array(1, $memberid, $personID, $personID, $memberid));
      if ($messageImg) {
        $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
      }
      $content = $MemberName . ": " . $messageText;
      $time = $db->getColumnData("SELECT MemberTime FROM members WHERE MemberID = ?", array($personID));
      $now_time = date("Y-m-d H:i:s");
      $strt = strtotime($time);
      $fnsh = strtotime($now_time);
      $diff = abs($fnsh - $strt);
      if ($diff < 10) {
        $styleicon = "style='color:green'";
      } else {
        $styleicon = "style='color:rgb(204, 1, 1)'";
      }
      if ($partID == $personID) {
        $styletext = 'style=""';
      } else {
        $styletext = 'style="opacity:1;"';
      }

      $resultcontent = $MemberName . ": " . $messageText;

      if (!$ishave) {
        $result["nonconversation"] = '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '">
                                        <div class="row my-2 justify-content-center align-items-center">
                                          <div class="col-2 text-center">
                                            <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                          </div>
                                          <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                            <div class="row fs-5">
                                              <div class="col-10 p-0 messenger-names">' . $name_lastname . '</div>
                                              <div class="col-2"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                            </div>
                                            <div class="row">
                                              <div class="col-8 p-0 text-start person-content" id="content_' . $personID . '" ' . $styletext . '>
                                                ' . $resultcontent . '
                                              </div>
                                              <div class="col-4 m-0 p-0 pe-1 text-end"><small>' . messageTime($messagetime) . '</small></div>
                                            </div>
                                          </div>
                                        </div>
                                      <hr class="my-3"></A>';
      } else {
        $result["conversationtrue"] = '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '">
                                          <div class="row my-2 justify-content-center align-items-center">
                                            <div class="col-2 text-center">
                                              <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                            </div>
                                            <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                              <div class="row fs-5">
                                                <div class="col-10 p-0 messenger-names">' . $name_lastname . '</div>
                                                <div class="col-2"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                              </div>
                                              <div class="row">
                                                <div class="col-8 p-0 text-start person-content" id="content_' . $personID . '" ' . $styletext . '>
                                                  ' . $resultcontent . '
                                                </div>
                                                <div class="col-4 m-0 p-0 pe-1 text-end"><small>' . messageTime($messagetime) . '</small></div>
                                              </div>
                                            </div>
                                          </div>
                                        <hr class="my-3"></A>';
      }
      $result["toast"] = '<a class="toast-link text-dark text-decoration-none" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '">
      <div class="toast show mt-2" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <img src="images_profile/' . $getprofileimg . '" width="30" height="30" class="rounded-circle me-2">
        <strong class="me-auto">' . $name_lastname . '</strong>
        <small class="text-muted">' . $translates["now"] . '</small>
      </div>
      <div class="toast-body">
      ' . $resultcontent . '
      </div>
    </div></a>';

      $result["personID"] = $personID;
      $db->Update("UPDATE messages SET MessageHasShown = ? WHERE MessageID = ?", array(1, $newmessageID));
    }
    $memberGroups = $db->getDatas("SELECT * FROM all_groups WHERE GroupMembers LIKE '%$memberid%'");
    foreach ($memberGroups as $eachgroup) {
      $newMessages2 = $db->getData("SELECT * FROM messages_group WHERE GroupID = ? AND MessageStatus = ? ORDER BY MessageID DESC", array($eachgroup->GroupID, 1));
      if ($newMessages2) {

        $ismessageshown = $newMessages2->MessageHasShown;
        $ismessageshown = explode(":", $ismessageshown);
        $result["info"] = $ismessageshown;
        if (!in_array($memberid, $ismessageshown) and $newMessages2->MessageFromID != $memberid) {
          $result["newMessages"] = "true";
          $newmessageID = $newMessages2->MessageID;
          $groupID = $newMessages2->GroupID;
          $messageText = $newMessages2->MessageText;
          $messageImg = $newMessages2->MessageImg;
          $messagetime = $newMessages2->MessageAddTime;
          $MemberName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($newMessages2->MessageFromID));
          $groupname = $eachgroup->GroupName;
          $groupimg = $eachgroup->GroupImage;
          if (is_null($groupimg)) {
            $groupimg = "noneimage.png";
          }
          if ($messageImg) {
            $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
          }
          $currentgroupID = security("GroupID");
          if ($currentgroupID == $groupID) {
            $styletext = 'style="opacity:0.5"';
          } else {
            $styletext = 'style="opacity:1;"';
          }
          $resultcontent = $MemberName . ": " . $messageText;


          $result["conversationtrue"] = '<a class="text-dark text-decoration-none" id="person_' . $groupID . '" href=\'http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $translates["group"] . '/' . $groupID . '\'>
                                        <div class="row my-2 justify-content-center align-items-center">
                                          <div class="col-2 text-center">
                                            <img src="group_images/' . $groupimg . '" class="rounded-circle" width="60" height="60">
                                          </div>
                                          <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                            <div class="row fs-5">
                                            <div class="col-12 p-0 messenger-names" id="chatbox_name_' . $groupID . '"><i class="fas fa-users" style="font-size: 17px;"></i> ';
          if ($groupname) {
            $result["conversationtrue"] .= $groupname;
          } else {
            $result["conversationtrue"] .= $translates["anonymousgrp"];
          }
          $result["conversationtrue"] .= '</div>
                                            </div>
                                            <div class="row">
                                              <div class="col-9 p-0 text-start person-content" id="content_' . $groupID . '" ' . $styletext . '>
                                                ' . $resultcontent . '
                                              </div>
                                                <div class="col-3 pe-1 text-end">
                                                 <small> ' . messageTime($messagetime) . '</small>
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                      <hr class="my-3"></A>';

          $result["toast"] = '<a class="toast-link text-dark text-decoration-none" href=\'http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $translates["group"] . '/' . $groupID . '\'>
          <div class="toast show mt-2" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="toast-header">
            <img src="group_images/' . $groupimg . '" width="30" height="30" class="rounded-circle me-2">
            <strong class="me-auto">' . $groupname . '</strong>
            <small class="text-muted">' . $translates["now"] . '</small>
          </div>
          <div class="toast-body">
          ' . $resultcontent . '
          </div>
        </div></a>';

          $result["personID"] = $groupID;
          $db->Update("UPDATE messages_group SET MessageHasShown = ? WHERE MessageID = ?", array(($memberid . ":"), $newmessageID));
        }
      }
    }
    echo json_encode($result);
    break;

  case 'sendmessage':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $messageText = security("messageText");
      $fromID = $_GET["FromID"];
      $toID = $_GET["ToID"];
      if (empty($messageText)) {
        $result["error"] = "error";
      } else {
        $saveMessage = $db->Insert("INSERT INTO messages
                                            SET MessageText = ?, MessageImg = ?, MessageFromID = ?, MessageToID = ?, MessageStatus = ?", array($messageText, null, $fromID, $toID, 1));
        if ($saveMessage) {
          $result["success"] = "success";
          $result["message"] = $messageText;
          $result["messageid"] = $saveMessage;

          $personID = $db->getColumnData("SELECT MessageToID FROM messages WHERE MessageID = ?", array($saveMessage));

          $isHaveChatBox = $db->getColumnData("SELECT ChatboxID FROM chatbox
                                                WHERE  (MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?)", array($memberid, $personID, $personID, $memberid));

          if ($isHaveChatBox) {
            $db->Update("UPDATE chatbox
                                    SET MessageStatus = ?, MessageFromID = ?, MessageToID = ?, LastTime = now(), MessageStatus = ?, MessageHasRead = ?
                                    WHERE ChatboxID = ?", array(1, $memberid, $personID, 1, 0, $isHaveChatBox));
          } else {
            $db->Insert("INSERT INTO chatbox
                                     SET MessageFromID = ?, MessageToID = ?, MessageStatus = ?, MessageHasRead = ?", array($memberid, $personID, 1, 0));
          }

          $getprofileimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
          $isPersonActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($personID));
          if ($isPersonActive != 1) {
            $getprofileimg = NULL;
          }
          $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
          if (is_null($getprofileimg)) {
            if ($gender == 'Male') {
              $getprofileimg = "profilemale.png";
            } else {
              $getprofileimg = "profilefemale.png";
            }
          }
          $name_lastname =  $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
          $messageText = $db->getColumnData("SELECT MessageText FROM messages WHERE MessageID = ?", array($saveMessage));
          $whosemessage =  $db->GetColumnData("SELECT MessageFromID FROM messages WHERE MessageID = ?", array($saveMessage));

          $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages
                                                        WHERE MessageID = ?", array($saveMessage));

          $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($messageID));
          if ($messageHasSeen == 1) {
            $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
          } else {
            $tic = '<i class="fas fa-check" style="font-size:13px;"></i>';
          }

          $result["lastsentmsg"] = '<li class="list-group-item bg-transparent p-4 py-1" style="border:none;" id="each_message_' . $saveMessage . '" lastid="' . $saveMessage . '">
                                      <div class="row d-flex flex-row-reverse"> 
                                        <div class="col-2 col-xl-1 ms-md-1 ms-xl-2 p-0 text-center d-flex justify-content-center align-items-center">
                                          <img src="images_profile/' . $member_profile . '"  class="rounded-circle shadow" width="50" height="50">
                                        </div>        
                                        <div class="p-2 text-end msg-container" style="width:auto;max-width:250px;min-width:75px;">
                                          <div class="me-2 del-msg"><i class="fas fa-trash position-absolute text-danger mt-2" onClick=\'DeleteMessage("deletemessage","' . $saveMessage . '")\'></i></div>
                                            <div class="d-flex text-start shadow flex-column row align-items-center bg-light text-dark rounded-3 d-flex flex-row flex-nowrap" style="height:100%;max-width:200px;">
                                              <div class="p-0 w-100" style="width:auto;max-width:200px;">
                                                <p class="m-0 py-1 px-2 fs-6 text-break">' . $messageText . '</p>
                                              </div>
                                              <div class="bg-light text-dark text-end px-2 rounded-3" style="border-top-left-radius:0px !important;border-top-right-radius:0px !important;font-size:12px">
                                                <span class="seentic me-1" id="tic_' . $saveMessage . '">' . $tic . '</span> 
                                                ' . messageTime($messagetime) . '
                                              </div>  
                                            </div> 
                                          </div>
                                        </div>
                                      </div>  
                                    </li>';


          $result["personID"] = $personID;

          $time = $db->getColumnData("SELECT MemberTime FROM members WHERE MemberID = ?", array($personID));
          $now_time = date("Y-m-d H:i:s");
          $strt = strtotime($time);
          $fnsh = strtotime($now_time);
          $diff = abs($fnsh - $strt);
          if ($diff < 10) {
            $styleicon = "style='color:green'";
          } else {
            $styleicon = "style='color:rgb(204, 1, 1)'";
          }
          $resultcontent = $translates["you"] . ": " . $messageText . " " . $tic;
          $ishave = $db->getData("SELECT * FROM messages WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))", array(1, $fromID, $toID, $toID, $fromID));
          if (!$ishave) {
            $result["nonconversation"] = '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '">
                                            <div class="row my-2 justify-content-center align-items-center">
                                              <div class="col-2 text-center">
                                                <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                              </div>
                                              <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                                <div class="row fs-5">
                                                  <div class="col-10 p-0 messenger-names">' . $name_lastname . '</div>
                                                  <div class="col-2"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                                </div>
                                                <div class="row">
                                                  <div class="col-8 p-0 text-start person-content" id="content_' . $personID . '" style="opacity:0.5">
                                                    ' . $resultcontent . '
                                                  </div>
                                                  <div class="col-4 m-0 p-0 pe-1 text-end"><small>' . messageTime($messagetime) . '</small></div>
                                                </div>
                                              </div>
                                            </div>
                                          <hr class="my-3"></A>';
          } else {
            $result["conversationtrue"] = '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '">
                                              <div class="row my-2 justify-content-center align-items-center">
                                                <div class="col-2 text-center">
                                                  <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                                </div>
                                                <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                                  <div class="row fs-5">
                                                    <div class="col-10 p-0 messenger-names">' . $name_lastname . '</div>
                                                    <div class="col-2"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col-8 p-0 text-start person-content" id="content_' . $personID . '" style="opacity:0.5">
                                                      ' . $resultcontent . '
                                                    </div>
                                                    <div class="col-4 m-0 p-0 pe-1 text-end"><small>' . messageTime($messagetime) . '</small></div>
                                                  </div>
                                                </div>
                                              </div>
                                            <hr class="my-3"></A>';
          }
        }
      }
    }
    echo json_encode($result);
    break;

  case 'sendmessage_group':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $messageText = security("messageText");
      $fromID = $_GET["FromID"];
      $groupID = $_GET["ToID"];
      if (empty($messageText)) {
        $result["error"] = "error";
      } else {
        $saveMessage = $db->Insert("INSERT INTO messages_group SET MessageText = ?, MessageFromID = ?, GroupID = ?, MessageStatus = ?", array($messageText, $fromID, $groupID, 1));
        if ($saveMessage) {
          $result["success"] = "success";
          $result["message"] = $messageText;
          $result["messageid"] = $saveMessage;

          $db->Update("UPDATE chatbox SET MessageFromID = ?, LastTime = now(), MessageHasRead = ? WHERE GroupID = ?", array($memberid, 0, $groupID));

          $groupimage = $db->getColumnData("SELECT GroupImage FROM all_groups WHERE GroupID = ?", array($groupID));
          if (is_null($groupimage)) {
            $groupimage = "noneimage.png";
          }
          $GroupName =  $db->getColumnData("SELECT GroupName FROM all_groups WHERE GroupID = ?", array($groupID));
          $messageText = $db->getColumnData("SELECT MessageText FROM messages_group WHERE MessageID = ?", array($saveMessage));

          $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages_group WHERE MessageID = ?", array($saveMessage));

          $result["lastsentmsg"] = '<li class="list-group-item bg-transparent p-4 py-1" style="border:none;" id="each_message_' . $saveMessage . '" lastid="' . $saveMessage . '">
                                        <div class="row d-flex flex-row-reverse"> 
                                          <div class="col-2 col-xl-1 ms-md-1 ms-xl-2 p-0 text-center d-flex justify-content-center align-items-center">
                                            <img src="images_profile/' . $member_profile . '"  class="rounded-circle shadow" width="50" height="50">
                                          </div>        
                                          <div class="p-2 text-end msg-container" style="width:auto;max-width:250px;min-width:75px;">
                                            <div class="me-2 del-msg"><i class="fas fa-trash position-absolute text-danger mt-2" onClick=\'DeleteMessage("deletemessage_group","' . $saveMessage . '")\'></i></div>
                                              <div class="d-flex text-start shadow flex-column row align-items-center bg-light text-dark rounded-3 d-flex flex-row flex-nowrap" style="height:100%;max-width:200px;">
                                                <div class="p-0 w-100" style="width:auto;max-width:200px;">
                                                  <p class="m-0 py-1 px-2 fs-6 text-break">' . $messageText . '</p>
                                                </div>
                                                <div class="bg-light text-dark text-end px-2 rounded-3" style="border-top-left-radius:0px !important;border-top-right-radius:0px !important;font-size:12px">
                                                  ' . messageTime($messagetime) . '
                                                </div>  
                                              </div> 
                                            </div>
                                          </div>
                                        </div>  
                                      </li>';


          $result["personID"] = $groupID;
          $resultcontent = $translates["you"] . ": " . $messageText;
          $result["conversationtrue"] = '<a class="text-dark text-decoration-none" id="person_' . $groupID . '" href\'http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $translates["group"] . '/' . $groupID . '\'>
                                          <div class="row my-2 justify-content-center align-items-center">
                                            <div class="col-2 text-center">
                                              <img src="group_images/' . $groupimage . '" class="rounded-circle" width="60" height="60">
                                            </div>
                                            <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                              <div class="row fs-5">
                                                <div class="col-12 p-0 messenger-names" id="chatbox_name_' . $groupID . '"><i class="fas fa-users" style="font-size: 17px;"></i> ';
          if ($GroupName) {
            $result["conversationtrue"] .= $GroupName;
          } else {
            $result["conversationtrue"] .= $translates["anonymousgrp"];
          }
          $result["conversationtrue"] .= '</div>
                                              </div>
                                              <div class="row">
                                                <div class="col-9 p-0 text-start person-content" id="content_' . $groupID . '" style="opacity:0.5">
                                                  ' . $resultcontent . '
                                                </div>
                                                  <div class="col-3" style="font-size:14px;">
                                                    ' . messageTime($messagetime) . '
                                                  </div>
                                              </div>
                                            </div>
                                          </div>
                                        <hr class="my-3"></A>';
        }
      }
    }
    echo json_encode($result);
    break;

  case 'sendimg':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $fromID = $_GET["FromID"];
      $toID = $_GET["ToID"];
      $img_name = $_FILES['img_message']['name'];
      $img_name_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
      $allowed_file_extensions = array("png", "jpg", "jpeg", "jfif");
      if (!in_array($img_name_ext, $allowed_file_extensions)) {
        $result["error"] = $translates["notallowedimg"];
      } else {
        $img_name = $user_name . "_" . $user_lastname . "_" . rand() . "." . $img_name_ext;
        $target = "message_images/" . basename($img_name);          // Hedefi images/resminismi yap
        $sql = $db->Insert("INSERT INTO messages
                            SET MessageText = ?, MessageImg = ?, MessageFromID = ?, MessageToID = ?, MessageStatus = ?", array(null, $img_name, $fromID, $toID, 1));

        move_uploaded_file($_FILES['img_message']['tmp_name'], $target);   //Dosyayı geçiçi yoldan hedef yola gönder

        $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($sql));
        if ($messageHasSeen == 1) {
          $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
        } else {
          $tic = '<i class="fas fa-check" style="font-size:13px;"></i>';
        }
        $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages WHERE MessageID = ?", array($sql));

        $imgmsg = '<img src="' . $target . '"  class="rounded-2" style="width:250px;min-height:20vh;">';

        $result["imgMsg"] = '<li class="list-group-item bg-transparent p-4 py-1" style="border:none;" id="each_message_' . $sql . '" lastid="' . $sql . '">
                              <div class="row d-flex flex-row-reverse">   
                                <div class="col-2 col-xl-1">
                                  <img src="images_profile/' . $member_profile . '"  class="rounded-circle" width="50" height="50">
                                </div>      
                                <div class="col-10 d-flex justify-content-end p-0 text-end message-content-img">
                                  <a class="w-33" href="' . $target . '">' . $imgmsg . '</a>
                                  <span class="time-img text-dark fs-6 m-2 p-1 align-self-start rounded-2 position-absolute" style="font-size: 13px !important;">
                                    <i class="fas fa-trash text-danger fs-6 del-img" onClick=\'DeleteMessage("deletemessage","' . $sql . '")\'></i>
                                    ' . messageTime($messagetime) . '
                                  </span>
                                  <span class="seentic time-img text-dark m-2 p-1 align-self-end rounded-2 position-absolute" style="font-size: 9px !important;">
                                  ' . $tic . '
                                  </span>
                                </div>
                                <script>baguetteBox.run(\'.message-content-img\');</script>
                              </div>
                            </li>';

        $personID = $toID;
        $result["personID"] = $personID;


        $isHaveChatBox = $db->getColumnData("SELECT ChatboxID FROM chatbox
                                            WHERE (MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?)", array($memberid, $personID, $personID, $memberid));

        if ($isHaveChatBox) {
          $db->Update("UPDATE chatbox
                    SET MessageStatus = ?, MessageFromID = ?, MessageToID = ?, LastTime = now(), MessageStatus = ?, MessageHasRead = ?
                    WHERE ChatboxID = ?", array(1, $memberid, $personID, 1, 0, $isHaveChatBox));
        } else {
          $db->Insert("INSERT INTO chatbox
                     SET MessageFromID = ?, MessageToID = ?, MessageStatus = ?, MessageHasRead = ?", array($memberid, $personID, 1, 0));
        }

        $getprofileimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
        $name_lastname =  $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));

        $isPersonActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($personID));
        if ($isPersonActive != 1) {
          $getprofileimg = NULL;
          $name_lastname = $translates["unknownuser"];
        }

        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
        if (is_null($getprofileimg)) {
          if ($gender == 'Male') {
            $getprofileimg = "profilemale.png";
          } else {
            $getprofileimg = "profilefemale.png";
          }
        }


        $time = $db->getColumnData("SELECT MemberTime FROM members WHERE MemberID = ?", array($personID));
        $now_time = date("Y-m-d H:i:s");
        $strt = strtotime($time);
        $fnsh = strtotime($now_time);
        $diff = abs($fnsh - $strt);
        if ($diff < 10) {
          $styleicon = "style='color:green'";
        } else {
          $styleicon = "style='color:rgb(204, 1, 1)'";
        }

        $resultcontent = $translates["you"] . ': ' . '<i class="fas fa-camera"></i> ' . $translates["photo"] . " " . $tic;

        $ishave = $db->getData("SELECT * FROM messages WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))", array(1, $fromID, $toID, $toID, $fromID));

        if (!$ishave) {
          $result["nonconversation"] = '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '" style=\'background:rgba(255, 255, 255, 0.2)\'>
                                          <div class="row my-2 justify-content-center align-items-center">
                                            <div class="col-2 text-center">
                                              <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                            </div>
                                            <div class="col-8 px-3 ps-md-5 ps-lg-4 ps-xl-5 ">
                                              <div class="row fs-5" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">' . $name_lastname . '</div>
                                              <div class="row">
                                                <div class="col-12 p-0 text-start person-content" id="content_' . $personID . '" style="opacity:0.5">
                                                ' . $resultcontent . '
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-2 text-center d-flex flex-column justify-content-between">
                                              <div class="row"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                              <div class="row" id="chatpersontime_' . $personID . '"><small>' . messageTime($messagetime) . '</small></div>
                                            </div>
                                          </div>
                                        <hr class="my-3"></A>';
        } else {
          $result["conversationtrue"] = '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '" style=\'background:rgba(255, 255, 255, 0.2)\'>
                                          <div class="row my-2 justify-content-center align-items-center">
                                            <div class="col-2 text-center">
                                              <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                            </div>
                                            <div class="col-8 px-3 ps-md-5 ps-lg-4 ps-xl-5 ">
                                              <div class="row fs-5" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">' . $name_lastname . '</div>
                                              <div class="row">
                                                <div class="col-12 p-0 text-start person-content" id="content_' . $personID . '" style="opacity:0.5">
                                                ' . $resultcontent . '
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-2 text-center d-flex flex-column justify-content-between">
                                              <div class="row"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                              <div class="row" id="chatpersontime_' . $personID . '"><small>' . messageTime($messagetime) . '</small></div>
                                            </div>
                                          </div>
                                        <hr class="my-3"></A>';
        }
      }
    }
    echo json_encode($result);
    break;

  case 'sendimg_group':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $fromID = $_GET["FromID"];
      $groupID = $_GET["ToID"];
      $img_name = $_FILES['img_message']['name'];
      $img_name_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
      $allowed_file_extensions = array("png", "jpg", "jpeg", "jfif");
      if (!in_array($img_name_ext, $allowed_file_extensions)) {
        $result["error"] = $translates["notallowedimg"];
      } else {
        $img_name = $user_name . "_" . $user_lastname . "_" . rand() . "." . $img_name_ext;
        $target = "message_images/" . basename($img_name);          // Hedefi images/resminismi yap
        $sql = $db->Insert("INSERT INTO messages_group
                              SET MessageImg = ?, MessageFromID = ?, GroupID = ?, MessageStatus = ?", array($img_name, $fromID, $groupID, 1));

        move_uploaded_file($_FILES['img_message']['tmp_name'], $target);   //Dosyayı geçiçi yoldan hedef yola gönder

        $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages_group WHERE MessageID = ?", array($sql));

        $imgmsg = '<img src="' . $target . '"  class="rounded-2" style="width:250px;min-height:20vh;">';

        $result["imgMsg"] = '<li class="list-group-item bg-transparent p-4 py-1" style="border:none;" id="each_message_' . $sql . '" lastid="' . $sql . '">
                                <div class="row d-flex flex-row-reverse">   
                                  <div class="col-2 col-xl-1">
                                    <img src="images_profile/' . $member_profile . '"  class="rounded-circle" width="50" height="50">
                                  </div>      
                                  <div class="col-10 d-flex justify-content-end p-0 text-end message-content-img">
                                    <a class="w-33" href="' . $target . '">' . $imgmsg . '</a>
                                    <span class="time-img text-dark fs-6 m-2 p-1 align-self-start rounded-2 position-absolute" style="font-size: 13px !important;">
                                      <i class="fas fa-trash text-danger fs-6 del-img" onClick=\'DeleteMessage("deletemessage_group","' . $sql . '")\'></i>
                                      ' . messageTime($messagetime) . '
                                    </span>
                                  </div>
                                  <script>baguetteBox.run(\'.message-content-img\');</script>
                                </div>
                              </li>';

        $result["personID"] = $groupID;

        $db->Update("UPDATE chatbox SET MessageFromID = ?, LastTime = now() WHERE GroupID = ?", array($memberid, $groupID));


        $groupimage = $db->getColumnData("SELECT GroupImage FROM all_groups WHERE GroupID = ?", array($groupID));
        if (is_null($groupimage)) {
          $groupimage = "noneimage.png";
        }
        $GroupName =  $db->getColumnData("SELECT GroupName FROM all_groups WHERE GroupID = ?", array($groupID));

        $resultcontent = $translates["you"] . ': ' . '<i class="fas fa-camera"></i> ' . $translates["photo"];

        $result["conversationtrue"] = '<a class="text-dark text-decoration-none" id="person_' . $groupID . '" href\'http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $translates["group"] . '/' . $groupID . '\'>
                                        <div class="row my-2 justify-content-center align-items-center">
                                          <div class="col-2 text-center">
                                            <img src="group_images/' . $groupimage . '" class="rounded-circle" width="60" height="60">
                                          </div>
                                          <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                            <div class="row fs-5">
                                            <div class="col-12 p-0 messenger-names" id="chatbox_name_' . $groupID . '"><i class="fas fa-users" style="font-size: 17px;"></i> ';
        if ($GroupName) {
          $result["conversationtrue"] .= $GroupName;
        } else {
          $result["conversationtrue"] .= $translates["anonymousgrp"];
        }
        $result["conversationtrue"] .= '</div>
                                            </div>
                                            <div class="row">
                                              <div class="col-9 p-0 text-start person-content" id="content_' . $personID . '" style="opacity:0.5">
                                                ' . $resultcontent . '
                                              </div>
                                                <div class="col-3" style="font-size:14px;">
                                                  ' . messageTime($messagetime) . '
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                      <hr class="my-3"></A>';
      }
    }
    echo json_encode($result);
    break;

  case 'deleteControl':
    $GroupID = security("GroupID");
    $personID = security("personID");
    $len = 0;
    if ($personID != $translates["group"]) {
      //Person 
      $deletedmessages = $db->getDatas("SELECT * FROM messages WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))", array(0, $memberid, $personID, $personID, $memberid));
      foreach ($deletedmessages as $deletedMsg) {
        $result["MessageID"] .= $deletedMsg->MessageID . " ";
        $len++;
      }
      $lastmessage = $db->getData("SELECT * FROM messages WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?)) ORDER BY MessageAddTime DESC", array(1, $memberid, $personID, $personID, $memberid));
      $messagetime = $lastmessage->MessageAddTime;
      $messagetime = "<small>" . messageTime($messagetime) . "</small>";
      $result["messagetime"] = $messagetime;
      if ($lastmessage->MessageFromID == $memberid) {
        $messageHasSeen = $lastmessage->MessageHasSeen;
        $messageText = $lastmessage->MessageText;
        $lastmsgimg = $lastmessage->MessageImg;
        if ($lastmsgimg) {
          $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
        }
        $result["lastcontent"] = $translates["you"] . ": " . $messageText;
      } else {
        $MemberName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($lastmessage->MessageFromID));
        $messageText = $lastmessage->MessageText;
        $lastmsgimg = $lastmessage->MessageImg;
        if ($lastmsgimg) {
          $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
        }
        $result["lastcontent"] = $MemberName . ": " . $messageText;
      }
      if (!$lastmessage) {
        $result["nomessage"] = "nomsg";
        $db->Update("UPDATE chatbox SET MessageStatus = ? WHERE (MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?)", array(0, $memberid, $personID, $personID, $memberid));
      }
      $result["personID"] = $personID;
      $result["len"] = $len;
    } else {
      //Group
      $deletedmessages = $db->getDatas("SELECT * FROM messages_group WHERE MessageStatus = ? AND GroupID = ?", array(0, $GroupID));
      foreach ($deletedmessages as $deletedMsg) {
        $result["MessageID"] .= $deletedMsg->MessageID . " ";
        $len++;
      }
      $lastmessage = $db->getData("SELECT * FROM messages_group WHERE MessageStatus = ? AND GroupID = ? ORDER BY MessageID DESC", array(1, $GroupID));
      $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages_group WHERE MessageID = ?", array($lastmessage->MessageID));
      $messagetime = "<small>" . messageTime($messagetime) . "</small>";
      $result["messagetime"] = $messagetime;
      if ($lastmessage->MessageFromID == $memberid) {
        $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages_group WHERE MessageID = ?", array($lastmessage->MessageID));
        $messageText = $lastmessage->MessageText;
        $lastmsgimg = $lastmessage->MessageImg;
        if ($lastmsgimg) {
          $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
        }
        $result["lastcontent"] = $translates["you"] . ": " . $messageText;
      } else {
        $MemberName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($lastmessage->MessageFromID));
        $messageText = $lastmessage->MessageText;
        $lastmsgimg = $lastmessage->MessageImg;
        if ($lastmsgimg) {
          $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
        }
        $result["lastcontent"] = $MemberName . ": " . $messageText;
      }
      if (!$lastmessage) {
        $groupCreatorID = $db->getColumnData("SELECT GroupCreator FROM all_groups WHERE GroupID = ?", array($GroupID));
        $CreatorName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($groupCreatorID));
        $result["lastcontent"] = $CreatorName . " " . $translates["personcreatedgroup"];
      }
      $result["personID"] = $GroupID;
      $result["len"] = $len;
    }



    echo json_encode($result);
    break;

  case 'deletemessage':
    $messageID = security("MessageID");
    $result["delete"] = $messageID;
    $db->Update("UPDATE messages SET MessageStatus = ? WHERE MessageID = ?", array(0, $messageID));

    $personID = $db->getColumnData("SELECT MessageToID FROM messages WHERE MessageID = ?", array($messageID));

    $anyMessageLeft = $db->getData("SELECT * FROM messages WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))", array(1, $memberid, $personID, $personID, $memberid));
    if (!$anyMessageLeft) {
      $result["nomessage"] = "nomsg";
      $result["personID"] = $personID;
      $db->Update("UPDATE chatbox SET MessageStatus = ? WHERE (MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?)", array(0, $memberid, $personID, $personID, $memberid));
    } else {
      $name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($personID));
      $name_lastname =  $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
      $newmessageID = $db->GetColumnData("SELECT MessageID FROM messages
                                            WHERE MessageStatus = ? AND ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))
                                            ORDER BY MessageAddTime DESC", array(1, $memberid, $personID, $personID, $memberid));

      $hasRead =  $db->GetColumnData("SELECT MessageHasRead FROM messages WHERE MessageID = ?", array($newmessageID));
      $db->Update("UPDATE chatbox SET MessageHasRead = ? 
                    WHERE ((MessageFromID = ? AND MessageToID = ?) OR (MessageFromID = ? AND MessageToID = ?))", array($hasRead, $memberid, $personID, $personID, $memberid));

      $messageText = $db->getColumnData("SELECT MessageText FROM messages WHERE MessageID = ?", array($newmessageID));
      $messageImg = $db->getColumnData("SELECT MessageImg FROM messages WHERE MessageID = ?", array($newmessageID));
      $whosemessage =  $db->GetColumnData("SELECT MessageFromID FROM messages WHERE MessageID = ?", array($newmessageID));


      $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages
                                                WHERE MessageID = ?", array($newmessageID));
      $messagetime = messageTime($messagetime);

      if ($whosemessage == $memberid) {
        $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($newmessageID));
        if ($messageHasSeen == 1) {
          $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
        } else {
          $tic = '<i class="fas fa-check" style="font-size:13px;"></i>';
        }
      } else {
        $tic = '';
      }

      $result["msgtime"] = $messagetime;
      $result["textcontrol"] = $messageText;
      $result["imgcontrol"] = $messageImg;
      if ($messageImg) {
        $result["personabs"] = ($whosemessage == $memberid  ? $translates["you"] : $name) . ": <i class='fas fa-camera'></i> " . $translates["photo"] . " " . $tic;
      } else {
        $result["personabs"] = ($whosemessage == $memberid  ? $translates["you"] : $name) . ": " . $messageText . " " . $tic;
      }
      $result["personID"] = $personID;
    }

    echo json_encode($result);
    break;

  case 'deletemessage_group':
    $messageID = security("MessageID");
    $result["delete"] = $messageID;
    $groupID = $db->getColumnData("SELECT GroupID FROM messages_group WHERE MessageID = ?", array($messageID));
    $deletemessage = $db->Update("UPDATE messages_group SET MessageStatus = ? WHERE MessageID = ?", array(0, $messageID));

    $newmessage = $db->GetData("SELECT * FROM messages_group WHERE MessageStatus = ? AND GroupID = ? ORDER BY MessageAddTime DESC", array(1, $groupID));

    $messageText = $newmessage->MessageText;
    $messageImg = $newmessage->MessageImg;
    $whosemessage = $newmessage->MessageFromID;
    $personName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($whosemessage));

    $messagetime = $newmessage->MessageAddTime;
    $messagetime = messageTime($messagetime);

    $result["msgtime"] = $messagetime;
    $result["textcontrol"] = $messageText;
    $result["imgcontrol"] = $messageImg;
    if ($messageImg) {
      $result["personabs"] = ($whosemessage == $memberid  ? $translates["you"] : $personName) . ": <i class='fas fa-camera'></i> " . $translates["photo"];
    } else {
      $result["personabs"] = ($whosemessage == $memberid  ? $translates["you"] : $personName) . ": " . $messageText;
    }
    $result["personID"] = $groupID;

    if (!$newmessage) {
      $groupCreatorID = $db->getColumnData("SELECT GroupCreator FROM all_groups WHERE GroupID = ?", array($groupID));
      $CreatorName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($groupCreatorID));
      $result["personabs"] = $CreatorName . " " . $translates["personcreatedgroup"];
      $db->Update("UPDATE chatbox SET MessageFromID = ? WHERE GroupID = ?", array(NULL, $groupID));
    }

    echo json_encode($result);
    break;

  case 'setStatus':
    $db->Update("UPDATE members SET MemberTime = now() WHERE MemberID = ?", array($memberid));
    break;

  case 'getStatus':
    $users = $db->getDatas("SELECT * FROM members");
    foreach ($users as $person) {
      $time = $person->MemberTime;
      $now_time = date("Y-m-d H:i:s");
      $strt = strtotime($time);
      $fnsh = strtotime($now_time);
      $diff = abs($fnsh - $strt);
      if ($diff < 10) {
        $output .= $person->MemberID . ":::";
      }
    }

    echo $output;
    break;

  case 'searchFriends':
    $searched_key = security("search");
    $addedFriends = security("addedFriends");
    $addedFriends = explode(":", $addedFriends);
    $searched_members = $db->getDatas("SELECT * FROM members WHERE MemberName LIKE '$searched_key%' AND MemberConfirm = ? ORDER BY MemberNames ASC", array(1));
    $result["friends"] = "";
    foreach ($searched_members as $member) {
      $friendID = $member->MemberID;
      if (!in_array($friendID, $addedFriends)) {
        $isfriend = $db->getData("SELECT * FROM friends WHERE (FirstMemberID = ? AND SecondMemberID = ?) OR (FirstMemberID = ? AND SecondMemberID = ?) AND FriendRequest = ?", array($friendID, $memberid, $memberid, $friendID, 1));
        $isPersonActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($friendID));
        if ($isfriend && $isPersonActive == 1) {
          $friendNames = $member->MemberNames;
          $friendIMG = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($friendID));
          $gender = $member->MemberGender;
          if (is_null($friendIMG)) {
            if ($gender == 'Male') {
              $friendIMG = "profilemale.png";
            } else {
              $friendIMG = "profilefemale.png";
            }
          }
          $result["friends"] .= '<li class="list-group-item each-friend" id="' . $friendID . '" style="cursor:pointer">
                                <img src="images_profile/' . $friendIMG . '" class="rounded-circle" style="width:40px;height:40px;">
                                <span>' . $friendNames . '</span>
                              </li>';
        }
      }
    }
    echo json_encode($result);
    break;

  case 'addedFriends':
    $friendID = security("FriendID");
    $friendIMG = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($friendID));
    $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($friendID));
    if (is_null($friendIMG)) {
      if ($gender == 'Male') {
        $friendIMG = "profilemale.png";
      } else {
        $friendIMG = "profilefemale.png";
      }
    }
    $result["addedFriends"] = '<div class="item carousel-div d-flex justify-content-center align-items-center mx-1 addedfriend" id="friendid_' . $friendID . '" style=\'width:50px;height:50px;background-image: url("images_profile/' . $friendIMG . '");\'>
                                <i class="fas fa-times text-danger removeaddedfriend" friendid="' . $friendID . '" style="font-size: 18px;"></i>
                              </div>';
    echo json_encode($result);
    break;

  case 'removeaddedFriend':
    $friendID = security("FriendID");
    $friendNames =  $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($friendID));
    $friendIMG = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($friendID));
    $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($friendID));
    if (is_null($friendIMG)) {
      if ($gender == 'Male') {
        $friendIMG = "profilemale.png";
      } else {
        $friendIMG = "profilefemale.png";
      }
    }
    $allAdded = security("allAdded");
    $allAdded = explode(":", $allAdded);
    foreach ($allAdded as $key => $value) {
      if ($value == $friendID) {
        $allAdded[$key] = "";
      }
    }
    $newallAdded = "";
    foreach ($allAdded as $key => $value) {
      if ($value != "") {
        $newallAdded .= $value . ":";
      }
    }

    $result["friends"] .= '<li class="list-group-item each-friend" id="' . $friendID . '" style="cursor:pointer">
                            <img src="images_profile/' . $friendIMG . '" class="rounded-circle" style="width:40px;height:40px;">
                            <span>' . $friendNames . '</span>
                          </li>';
    $result["alladded"] = $newallAdded;
    echo json_encode($result);
    break;

  case 'createGroup':
    $groupname = security("groupname");
    $groupimg = $_FILES["groupimg"]['name'];
    $groupexp = security("groupexp");
    $GroupMembers = security("GroupMembers");
    if ($GroupMembers) {
      $GroupMembers = $memberid . ":" . $GroupMembers;
      $membernumber = strlen($GroupMembers);
    }
    if ($groupexp == "") {
      $groupexp = null;
    }
    if (empty($groupname) or empty($GroupMembers)) {
      $result["error"] = $translates["empty"];
    } else {
      if ($membernumber < 5) {
        $result["error"] = $translates["atleast2person"];
      } else {
        if ($groupimg) {
          $groupimg_ext = strtolower(pathinfo($groupimg, PATHINFO_EXTENSION));
          $allowed_file_extensions = array("png", "jpg", "jpeg", "jfif");
          if (!in_array($groupimg_ext, $allowed_file_extensions)) {
            $result["error"] = "Sadece jpeg, jpg, png ve jfif uzantılı dosya yükleyebilirsiniz.";
          } else {
            $groupimgname = preg_replace("/ /", "_", $groupname);
            $groupimg = $groupimgname  . "_" . uniqid() . "." . $groupimg_ext;
            $target = "group_images/" . basename($groupimg);
          }
          move_uploaded_file($_FILES['groupimg']['tmp_name'], $target);
        } else {
          $groupimg = "noneimage.png";
        }
        $addtogroups = $db->Insert("INSERT INTO all_groups SET GroupName = ?,
        GroupImage = ?,
        GroupCreator = ?,
        GroupAdmins = ?,
        GroupMembers = ?,
        GroupExplanation = ?", array($groupname, $groupimg, $memberid, $memberid . ":", $GroupMembers, $groupexp));
        $addtochatbox = $db->Insert("INSERT INTO chatbox SET GroupID = ?, GroupMembers = ?", array($addtogroups, $GroupMembers));
        $result["success"] = $translates["grouphascreated"];
        $groupMessage = $user_name . " " . $translates["personcreatedgroup"];
        $result["groupcontact"] = '<a class="text-dark text-decoration-none" id="group_' . $addtogroups . '" href=\'http://localhost/aybu/socialmedia/' . $translates["messages"] . '/' . $translates["group"] . '/' . $addtogroups . '\'>
                                    <div class="row my-2 justify-content-center align-items-center">
                                      <div class="col-2 text-center">
                                        <img src="group_images/' . $groupimg . '" class="rounded-circle" width="60" height="60">
                                      </div>
                                      <div class="col-8 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                        <div class="row fs-5">
                                        <div class="col-12 p-0 messenger-names" id="chatbox_name_' . $addtogroups . '"><i class="fas fa-users" style="font-size: 17px;"></i> ';
        if ($groupname) {
          $result["groupcontact"] .= $groupname;
        } else {
          $result["groupcontact"] .= $translates["anonymousgrp"];
        }
        $result["groupcontact"] .= '</div>
                                        </div>
                                        <div class="row">
                                          <div class="col-12 p-0 text-start person-content" id="content_' . $addtogroups . '">
                                            ' . $groupMessage . '
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-2 text-center d-flex flex-column justify-content-between">
                                      </div>
                                    </div>
                                  <hr class="my-3"></A>';
      }
    }
    echo json_encode($result);
    break;

  case 'changeDesc':
    $newDesc = security("newDesc");
    $groupID = security("groupID");
    $db->Update("UPDATE all_groups SET GroupExplanation = ? WHERE GroupID = ?", array($newDesc, $groupID));
    echo json_encode($result);
    break;

  case 'changeName':
    $newName = security("newName");
    $groupID = security("groupID");
    $db->Update("UPDATE all_groups SET GroupName = ? WHERE GroupID = ?", array($newName, $groupID));
    echo json_encode($result);
    break;

  case 'removeMember':
    $groupID = security("groupID");
    $personID = security("MemberID");
    $newMembers  = "";
    $newAdmins  = "";
    $allmembers = $db->getColumnData("SELECT GroupMembers FROM all_groups WHERE GroupID = ?", array($groupID));
    $allmembers = explode(":", $allmembers);
    $groupMembersNum = count($allmembers);
    unset($allmembers[$groupMembersNum - 1]);
    foreach ($allmembers as $eachmemberid) {
      if ($eachmemberid != $personID) {
        $newMembers .= $eachmemberid . ":";
      }
    }
    $alladmins = $db->getColumnData("SELECT GroupAdmins FROM all_groups WHERE GroupID = ?", array($groupID));
    $alladmins = explode(":", $alladmins);
    $groupAdminsNum = count($alladmins);
    unset($alladmins[$groupAdminsNum - 1]);
    foreach ($alladmins as $eachadminid) {
      if ($eachadminid != $personID) {
        $newAdmins .= $eachadminid . ":";
      }
    }
    $db->Update("UPDATE all_groups SET GroupAdmins = ?, GroupMembers = ? WHERE GroupID = ?", array($newAdmins, $newMembers, $groupID));
    $result["newNum"] = ($groupMembersNum - 2) . " " . $translates["people"];
    echo json_encode($result);
    break;

  case 'demoteMember':
    $groupID = security("groupID");
    $personID = security("MemberID");
    $newAdmins = "";
    $alladmins = $db->getColumnData("SELECT GroupAdmins FROM all_groups WHERE GroupID = ?", array($groupID));
    $alladmins = explode(":", $alladmins);
    $groupAdminsNum = count($alladmins);
    unset($alladmins[$groupAdminsNum - 1]);
    foreach ($alladmins as $eachadminid) {
      if ($eachadminid != $personID) {
        $newAdmins .= $eachadminid . ":";
      }
    }
    $db->Update("UPDATE all_groups SET GroupAdmins = ? WHERE GroupID = ?", array($newAdmins,  $groupID));
    echo json_encode($result);
    break;

  case 'promoteMember':
    $groupID = security("groupID");
    $personID = security("MemberID");
    $alladmins = $db->getColumnData("SELECT GroupAdmins FROM all_groups WHERE GroupID = ?", array($groupID));
    $alladmins = $alladmins .  $personID . ":";
    $db->Update("UPDATE all_groups SET GroupAdmins = ? WHERE GroupID = ?", array($alladmins,  $groupID));
    echo json_encode($result);
    break;

  case 'searchMember':
    $groupID = security("groupID");
    $searched_key = security("searchedKey");
    $member_searched = $db->getDatas("SELECT * FROM members WHERE MemberNames LIKE '$searched_key%' AND MemberConfirm = ?", array(1));
    foreach ($member_searched as $item) {
      $memberID = $item->MemberID;
      $isadmin = 0;
      $amiadmin = 1;
      $GroupMembers = $db->getColumnData("SELECT GroupMembers FROM all_groups WHERE GroupID = ?", array($groupID));
      $GroupMembers = explode(":", $GroupMembers);
      $groupMembersNum = count($GroupMembers);
      unset($GroupMembers[$groupMembersNum - 1]);
      foreach ($GroupMembers as $eachmemberid) {
        if ($eachmemberid == $memberID) {
          if ($item->MemberConfirm == 1) {
            $person_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberID));
            $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($memberID));
            if (is_null($person_photo)) {
              if ($gender == 'Male') {
                $person_photo = "profilemale.png";
              } else {
                $person_photo = "profilefemale.png";
              }
            }
            $result["members"] .= '<div class="col-12 border py-2" id="groupMember_' . $memberID . '">
                                      <div class="row">
                                        <div class="col-2">
                                          <img src="images_profile/' . $person_photo . '" style="width:50px;height:50px;" class="rounded-circle border">
                                        </div>
                                        <div class="col-5 m-0 p-0 d-flex justify-content-start align-items-center fs-5">
                                          <span>' . $item->MemberNames . '</span>
                                        </div>
                                        <div class="col-5 p-0 m-0 pe-3 d-flex align-items-center justify-content-end">';
            $admins = $db->getColumnData("SELECT GroupAdmins FROM all_groups WHERE GroupID = ?", array($groupID));
            $admins = explode(":", $admins);
            foreach ($admins as $admin) {
              if ($admin == $memberID) {
                $isadmin = 1;
              }
            }
            if ($isadmin) {
              $result["members"] .= '<span class="p-1 rounded-1" style="color:green;border:1px solid green;font-size:12px" id="admin_' . $memberID . '">' . $translates["gradmin"] . '</span>';
              if ($memberID != $memberid) {
                $result["members"] .= '<button type="button" class="btn btn-sm ms-2 btn-outline-warning demoteMember" id="division_' . $memberID . '" groupid="' . $groupID . '" memberid="' . $memberID . '"><i class="fas fa-angle-double-down px-1"></i></button>';
              }
            } else {
              if ($memberID != $memberid) {
                $result["members"] .= '<button type="button" class="btn btn-sm ms-2 btn-outline-success promoteMember" id="division_' . $memberID . '" groupid="' . $groupID . '" memberid="' . $memberID . '"><i class="fas fa-angle-double-up px-1"></i></button>';
              }
            }
            foreach ($admins as $admin) {
              if ($admin == $memberid) {
                $amiadmin = 1;
              }
            }
            if ($amiadmin && $memberID != $memberid) {
              $result["members"] .= '<button type="button" class="btn btn-sm ms-2 btn-outline-danger removeMember" groupid="' . $groupID . '" memberid="' . $memberID . '"><i class="fas fa-user-slash"></i></button>';
            }
            $result["members"] .= '</div>
                                      </div>
                                    </div>';
          }
        }
      }
    }
    echo json_encode($result);
    break;

  case 'searchallMembers':
    $groupID = security("groupID");
    $searched_key = security("searchedKey");
    $member_searched = $db->getDatas("SELECT * FROM members WHERE MemberNames LIKE '$searched_key%' AND MemberConfirm = ? ORDER BY MemberName", array(1));
    foreach ($member_searched as $item) {
      $memberID = $item->MemberID;
      $isMember = 0;
      $GroupMembers = $db->getColumnData("SELECT GroupMembers FROM all_groups WHERE GroupID = ?", array($groupID));
      $GroupMembers = explode(":", $GroupMembers);
      foreach ($GroupMembers as $eachmemberid) {
        if ($eachmemberid == $memberID) {
          $isMember = 1;
        }
      }
      if (!$isMember) {
        $person_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberID));
        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($memberID));
        $memberNames = $item->MemberNames;
        if (is_null($person_photo)) {
          if ($gender == 'Male') {
            $person_photo = "profilemale.png";
          } else {
            $person_photo = "profilefemale.png";
          }
        }
        $result["members"] .= '<div class="col-11 mx-auto border m-0 py-2" id="allMembers_' . $memberID . '">
                                <div class="row">
                                  <div class="col-2">
                                    <img src="images_profile/' . $person_photo . '" style="width:50px;height:50px;" class="rounded-circle border">
                                  </div>
                                  <div class="col-7 m-0 p-0 d-flex justify-content-start align-items-center fs-5">
                                    <span>' . $memberNames . '</span>
                                  </div>
                                  <div class="col-3 d-flex justify-content-end align-items-center">
                                    <div class="border d-flex justify-content-center align-items-center text-success addMemberIcon" id="operation_' . $memberID . '" memberid="' . $memberID . '" groupid="' . $groupID . '">
                                      <i class="fas fa-plus" id="icon_' . $memberID . '"></i>
                                    </div>
                                  </div>
                                </div>
                              </div>';
      }
    }
    echo json_encode($result);
    break;

  case 'addMember':
    $groupID = security("groupID");
    $personID = security("MemberID");
    $GroupMembers = $db->getColumnData("SELECT GroupMembers FROM all_groups WHERE GroupID = ?", array($groupID));
    $GroupMembers = $GroupMembers . $personID . ":";
    $GroupMembers = $db->Update("UPDATE all_groups SET GroupMembers = ? WHERE GroupID = ?", array($GroupMembers, $groupID));
    echo json_encode($result);
    break;

  case 'removeMember':
    $groupID = security("groupID");
    $personID = security("MemberID");
    $newMembers = "";
    $allmembers = $db->getColumnData("SELECT GroupMembers FROM all_groups WHERE GroupID = ?", array($groupID));
    $allmembers = explode(":", $allmembers);
    $groupMembersNum = count($allmembers);
    unset($allmembers[$groupMembersNum - 1]);
    foreach ($allmembers as $eachmemberid) {
      if ($eachmemberid != $personID) {
        $newMembers .= $eachmemberid . ":";
      }
    }
    $db->Update("UPDATE all_groups SET GroupAdmins = ? WHERE GroupID = ?", array($newMembers,  $groupID));
    echo json_encode($result);
    break;

  case 'changeImg':
    $groupID = security("groupID");
    $groupimg = $_FILES["upload_groupimg"]['name'];
    $oldimage = $db->getColumnData("SELECT GroupImage FROM all_groups WHERE GroupID = ?", array($groupID));
    if ($oldimage) {
      $delete_img = "group_images/" . $oldimage;
      unlink($delete_img);
    }
    if ($groupimg) {
      $groupimg_ext = strtolower(pathinfo($groupimg, PATHINFO_EXTENSION));
      $allowed_file_extensions = array("png", "jpg", "jpeg", "jfif");
      if (!in_array($groupimg_ext, $allowed_file_extensions)) {
        $result["error"] = "Sadece jpeg, jpg, png ve jfif uzantılı dosya yükleyebilirsiniz.";
      } else {
        $groupname = $db->getColumnData("SELECT GroupName FROM all_groups WHERE GroupID = ?", array($groupID));
        $groupimgname = preg_replace("/ /", "_", $groupname);
        $groupimg = $groupimgname  . "_" . uniqid() . "." . $groupimg_ext;
        $target = "group_images/" . basename($groupimg);
      }
      move_uploaded_file($_FILES['upload_groupimg']['tmp_name'], $target);
    } else {
      $groupimg = "noneimage.png";
    }
    $changeimg = $db->Update("UPDATE all_groups SET GroupImage = ? WHERE GroupID = ?", array($groupimg, $groupID));
    $result["imgsrc"] = $target;
    echo json_encode($result);
    break;

  case 'leaveGroup':
    $groupID = security("groupID");
    $group = $db->getData("SELECT * FROM all_groups WHERE GroupID = ?", array($groupID));

    $GroupMembers = $group->GroupMembers;
    $GroupMembers = explode(":", $GroupMembers);
    $groupMembersNum = count($GroupMembers);
    $newMembers = "";
    foreach ($GroupMembers as $eachMember) {
      if ($eachMember != $memberid && $eachMember != "") {
        $newMembers .= $eachMember . ":";
      }
    }

    $groupAdmins = $group->GroupAdmins;
    $groupAdmins = explode(":", $groupAdmins);
    $groupAdminsNum = count($groupAdmins);
    $amiadmin = 0;
    $newAdmins = "";
    foreach ($groupAdmins as $eachAdmin) {
      if ($memberid == $eachAdmin) {
        $amiadmin = 1;
      } else {
        if ($eachAdmin != "") {
          $newAdmins .= $eachAdmin . ":";
        }
      }
    }


    if ($groupAdminsNum == 2 && $amiadmin == 1) {
      if ($groupMembersNum == 2) {
        $db->Delete("DELETE FROM all_groups WHERE GroupID = ?", array($groupID));
      } else {
        $newMembersArr = explode(":", $newMembers);
        $newAdmins = $newMembersArr[0] . ":";
        $db->Update("UPDATE all_groups SET GroupAdmins = ?, GroupMembers = ? WHERE GroupID = ?", array($newAdmins, $newMembers, $groupID));
        $db->Update("UPDATE chatbox SET GroupMembers = ? WHERE GroupID = ?", array($newMembers, $groupID));
      }
    } else {
      $db->Update("UPDATE all_groups SET GroupAdmins = ?, GroupMembers = ? WHERE GroupID = ?", array($newAdmins, $newMembers, $groupID));
      $db->Update("UPDATE chatbox SET GroupMembers = ? WHERE GroupID = ?", array($newMembers, $groupID));
    }
    echo json_encode($result);
    break;
}
