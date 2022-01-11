<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "functions/time.php";
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";
require_once "functions/seolink.php";

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

$output = array();

$operation = security("Operation");

switch ($operation) {
  case 'headersearch':

    if (security("search")) {
      $searched_key = security("search");
    }
    $output["total"] = $db->getColumnData("SELECT COUNT(*) FROM members WHERE MemberNames LIKE '$searched_key%'");
    $member_searched = $db->getDatas("SELECT * FROM members WHERE MemberNames LIKE '$searched_key%' LIMIT 2");

    foreach ($member_searched as $item) {
      if ($item->MemberConfirm == 1) {
        $person_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($item->MemberID));
        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($item->MemberID));
        if (is_null($person_photo)) {
          if ($gender == 'Male') {
            $person_photo = "profilemale.png";
          } else {
            $person_photo = "profilefemale.png";
          }
        }
        $output["data"] .= "<a class='list-group-item' href=\"http://localhost/aybu/socialmedia/" . $translates['profile'] . "/" . $item->MemberID . "\">
                              <img src='images_profile/" . $person_photo . "' class='rounded-circle' style='width: 50px;height:50px;margin-right:10px;'>" . $item->MemberName . " " . $item->MemberLastName . "
                            </a>";
      }
    }

    $output["key"] = seolink($searched_key);
    echo json_encode($output);
    break;

  case 'messagessearch':
    $searched_key = security("search");
    if ($searched_key != "") {
      $output["state"] = "boş değil";
      $chatpersons = $db->getDatas("SELECT * FROM chatbox
                                  WHERE MessageStatus = 1 
                                  AND (MessageFromID = ? OR MessageToID = ? OR GroupMembers LIKE '%$memberid%')", array($memberid, $memberid));
      foreach ($chatpersons as $info) {
        $groupID = $info->GroupID;
        if ($groupID) {
          $groupInfos = $db->getData("SELECT * FROM all_groups WHERE GroupID = ?", array($groupID));
          $groupName = $groupInfos->GroupName;
          $pattern = "/$searched_key/i";
          if (preg_match($pattern, $groupName)) {
            if (!$info->MessageFromID) {
              $groupCreatorID = $groupInfos->GroupCreator;
              $CreatorName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($groupCreatorID));
              $groupMessage = $CreatorName . " " . $translates["personcreatedgroup"];
            } else {
              $lastmessage = $db->getData("SELECT * FROM messages_group WHERE GroupID = ? AND MessageStatus = ? ORDER BY MessageAddTime DESC", array($groupID, 1));
              $whosemessage = $lastmessage->MessageFromID;
              if ($whosemessage == $memberid) {
                $fromwho = $translates["you"];
              } else {
                $fromwho = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($whosemessage));
              }
              if ($lastmessage->MessageImg) {
                $groupMessage = $fromwho . ": " . '<i class="fas fa-camera"></i> ' . $translates["photo"];
              } else {
                $groupMessage = $fromwho . ": " . $lastmessage->MessageText;
              }
              $messageHasRead = $info->MessageHasRead;
              $messageHasRead = explode(":", $messageHasRead);
              if (!in_array($memberid, $messageHasRead) && $groupID != $edit && $memberid != $whosemessage) {
                $styleperson = "style='opacity:1'";
              } else {
                $styleperson = "style='opacity:0.5'";
              }
            }

            $groupID = $groupInfos->GroupID;
            $groupimg = $groupInfos->GroupImage;
            if (is_null($groupimg)) {
              $groupimg = "noneimage.png";
            }
            $groupName = $groupInfos->GroupName;

            $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages_group WHERE GroupID = ? AND MessageStatus = ? ORDER BY MessageAddTime DESC", array($groupID, 1));
            $output["data"] .= '<a class="text-dark text-decoration-none" id="person_' . $groupID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $translates["group"] . '/' . $groupID . '"><div class="row my-2 justify-content-center align-items-center">
                                  <div class="col-2 text-center">
                                    <img src="group_images/' . $groupimg . '" class="rounded-circle" width="60" height="60">
                                  </div>
                                  <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                    <div class="row fs-5">
                                      <div class="col-12 p-0 messenger-names" id="chatbox_name_' . $groupID . '"><i class="fas fa-users" style="font-size: 17px;"></i> ';
            if ($groupName) {
              $output["data"] .=  $groupName;
            } else {
              $output["data"] .= $translates["anonymousgrp"];
            }
            $output["data"] .= '</div>
                                    </div>
                                    <div class="row">
                                      <div class="col-9 p-0 text-start person-content" id="content_' . $groupID . '" ' . $styleperson . '>
                                        ' . $groupMessage . '
                                      </div>';
            if ($info->MessageFromID) {

              $output["data"] .= '<div class="col-3 pe-1 text-end">
                                          <small>' . messageTime($messagetime) . '</small>
                                        </div>';
            }
            $output["data"] .= '</div>
                                  </div>
                                </div>
                              </a>';
          }
        } else {
          if ($info->MessageFromID == $memberid) {
            $personID = $info->MessageToID;
          } else {
            $personID = $info->MessageFromID;
          }
          $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
          $pattern = "/$searched_key/i";
          if (preg_match($pattern, $personNames)) {
            $getprofileimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
            $ChatPersonName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($personID));
            $ChatPersonLastName = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = ?", array($personID));
            if (is_null($getprofileimg)) {
              if ($gender == 'Male') {
                $getprofileimg = "profilemale.png";
              } else {
                $getprofileimg = "profilefemale.png";
              }
            }

            $name_lastname = $ChatPersonName . " " . $ChatPersonLastName;

            $messageID = $db->GetColumnData("SELECT MessageID FROM messages
                 WHERE MessageStatus = 1 AND ((MessageFromID = $memberid AND MessageToID = $personID) OR (MessageFromID = $personID AND MessageToID = $memberid))
                 ORDER BY MessageAddTime DESC");

            $messageText = $db->getColumnData("SELECT MessageText FROM messages WHERE MessageID = ?", array($messageID));
            $messageImg = $db->getColumnData("SELECT MessageImg FROM messages WHERE MessageID = ?", array($messageID));

            $whosemessage = $db->GetColumnData("SELECT MessageFromID FROM messages WHERE MessageID = ?", array($messageID));

            $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages
                 WHERE MessageID = ? AND MessageStatus = ?", array($messageID, 1));

            if ($whosemessage != $memberid) {
              $messageHasRead = $info->MessageHasRead;
            } else {
              $messageHasRead = 1;
            }

            if ($whosemessage == $memberid) {
              $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($messageID));
              if ($messageHasSeen == 1) {
                $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
              } else {
                $tic = ' <i class="fas fa-check" style="font-size:13px;"></i>';
              }
              $fromwho = $translates["you"];
            } else {
              $fromwho = $ChatPersonName;
              $tic = '';
            }

            $time = $db->getColumnData("SELECT MemberTime FROM members WHERE MemberID = ?", array($personID));
            $now_time = date("Y-m-d H:i:s");
            $strt = strtotime($time);
            $fnsh = strtotime($now_time);
            $diff = abs($fnsh - $strt);
            if ($diff < 10) {
              $result = "style='color:green'";
            } else {
              $result = "style='color:rgb(204, 1, 1)'";
            }
            if ($messageImg) {
              $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
            }

            $resultcontent = $fromwho . ": " . $messageText . $tic;
            $output["data"] .= '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '"><div class="row my-2 justify-content-center align-items-center">
                                <div class="col-2 text-center">
                                  <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                </div>
                                <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                                  <div class="row fs-5">
                                    <div class="col-10 p-0 messenger-names">' . $name_lastname . '</div>
                                    <div class="col-2"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $result . '></i></div>
                                  </div>
                                  <div class="row">
                                    <div class="col-8 p-0 text-start person-content" id="content_' . $personID . '" ' . $styleperson . '>
                                      ' . $resultcontent . '
                                    </div>
                                    <div class="col-4 m-0 p-0 pe-1 text-end"><small>' . messageTime($messagetime) . '</small></div>
                                  </div>
                                </div>
                              </div>
                            </a>';
          }
        }
      }
    } else { //Arama yeri boşsa
      $chatpersons = $db->getDatas("SELECT * FROM chatbox
      WHERE MessageStatus = 1 AND (MessageFromID = $memberid OR MessageToID = $memberid OR GroupMembers LIKE '%$memberid%')
      ORDER BY LastTime DESC");
      foreach ($chatpersons as $info) {
        $groupID = $info->GroupID;
        if ($groupID) {
          $groupInfos = $db->getData("SELECT * FROM all_groups WHERE GroupID = ?", array($groupID));
          if (!$info->MessageFromID) {
            $groupCreatorID = $groupInfos->GroupCreator;
            $CreatorName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($groupCreatorID));
            $groupMessage = $CreatorName . " " . $translates["personcreatedgroup"];
          } else {
            $lastmessage = $db->getData("SELECT * FROM messages_group WHERE GroupID = ? AND MessageStatus = ? ORDER BY MessageAddTime DESC", array($groupID, 1));
            $whosemessage = $lastmessage->MessageFromID;
            if ($whosemessage == $memberid) {
              $fromwho = $translates["you"];
            } else {
              $fromwho = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($whosemessage));
            }
            if ($lastmessage->MessageImg) {
              $groupMessage = $fromwho . ": " . '<i class="fas fa-camera"></i> ' . $translates["photo"];
            } else {
              $groupMessage = $fromwho . ": " . $lastmessage->MessageText;
            }
            $messageHasRead = $info->MessageHasRead;
            $messageHasRead = explode(":", $messageHasRead);
            if (!in_array($memberid, $messageHasRead) && $groupID != $edit && $memberid != $whosemessage) {
              $styleperson = "style='opacity:1'";
            } else {
              $styleperson = "style='opacity:0.5'";
            }
          }

          $groupID = $groupInfos->GroupID;
          $groupimg = $groupInfos->GroupImage;
          if (is_null($groupimg)) {
            $groupimg = "noneimage.png";
          }
          $groupName = $groupInfos->GroupName;

          $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages_group WHERE GroupID = ? AND MessageStatus = ? ORDER BY MessageAddTime DESC", array($groupID, 1));

          $output["sa"] .=  $groupID;
          $output["data"] .= '<a class="text-dark text-decoration-none" id="person_' . $groupID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $translates["group"] . '/' . $groupID . '"><div class="row my-2 justify-content-center align-items-center">
              <div class="col-2 text-center">
                <img src="group_images/' . $groupimg . '" class="rounded-circle" width="60" height="60">
              </div>
              <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                <div class="row fs-5">
                  <div class="col-12 p-0 messenger-names" id="chatbox_name_' . $groupID . '"><i class="fas fa-users" style="font-size: 17px;"></i> ';
          if ($groupName) {
            $output["data"] .= $groupName;
          } else {
            $output["data"] .= $translates["anonymousgrp"];
          }
          $output["data"] .= '</div>
                </div>
                <div class="row">
                  <div class="col-9 p-0 text-start person-content" id="content_' . $groupID . '" ' . $styleperson . '>
                    ' . $groupMessage . '
                  </div>';
          if ($info->MessageFromID) {
            $output["data"] .= '<div class="col-3 pe-1 text-end">
                      <small>' . messageTime($messagetime) . '</small>
                    </div>';
          }
          $output["data"] .= '</div>
              </div>
            </div>
          </a>';
        } else {
          if ($info->MessageFromID == $memberid) {
            $personID = $info->MessageToID;
          } else {
            $personID = $info->MessageFromID;
          }
          $getprofileimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
          $ChatPersonName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($personID));
          $ChatPersonLastName = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = ?", array($personID));
          if (is_null($getprofileimg)) {
            if ($gender == 'Male') {
              $getprofileimg = "profilemale.png";
            } else {
              $getprofileimg = "profilefemale.png";
            }
          }

          $name_lastname = $ChatPersonName . " " . $ChatPersonLastName;

          $messageID = $db->GetColumnData("SELECT MessageID FROM messages
                   WHERE MessageStatus = 1 AND ((MessageFromID = $memberid AND MessageToID = $personID) OR (MessageFromID = $personID AND MessageToID = $memberid))
                   ORDER BY MessageAddTime DESC");

          $messageText = $db->getColumnData("SELECT MessageText FROM messages WHERE MessageID = ?", array($messageID));
          $messageImg = $db->getColumnData("SELECT MessageImg FROM messages WHERE MessageID = ?", array($messageID));

          $whosemessage = $db->GetColumnData("SELECT MessageFromID FROM messages WHERE MessageID = ?", array($messageID));

          $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages
                   WHERE MessageID = ? AND MessageStatus = ?", array($messageID, 1));

          if ($whosemessage != $memberid) {
            $messageHasRead = $info->MessageHasRead;
          } else {
            $messageHasRead = 1;
          }

          if ($whosemessage == $memberid) {
            $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($messageID));
            if ($messageHasSeen == 1) {
              $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
            } else {
              $tic = ' <i class="fas fa-check" style="font-size:13px;"></i>';
            }
            $fromwho = $translates["you"];
          } else {
            $fromwho = $ChatPersonName;
            $tic = '';
          }

          $time = $db->getColumnData("SELECT MemberTime FROM members WHERE MemberID = ?", array($personID));
          $now_time = date("Y-m-d H:i:s");
          $strt = strtotime($time);
          $fnsh = strtotime($now_time);
          $diff = abs($fnsh - $strt);
          if ($diff < 10) {
            $result = "style='color:green'";
          } else {
            $result = "style='color:rgb(204, 1, 1)'";
          }
          if ($messageImg) {
            $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
          }

          $resultcontent = $fromwho . ": " . $messageText . $tic;
          $output["data"] .= '<a class="text-dark text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '"><div class="row my-2 justify-content-center align-items-center">
              <div class="col-2 text-center">
                <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
              </div>
              <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                <div class="row fs-5">
                  <div class="col-10 p-0 messenger-names">' . $name_lastname . '</div>
                  <div class="col-2"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $result . '></i></div>
                </div>
                <div class="row">
                  <div class="col-8 p-0 text-start person-content" id="content_' . $personID . '" ' . $styleperson . '>
                    ' . $resultcontent . '
                  </div>
                  <div class="col-4 m-0 p-0 pe-1 text-end"><small>' . messageTime($messagetime) . '</small></div>
                </div>
              </div>
            </div>
          </a>';
        }
      }
    }
    echo json_encode($output);
    break;
}
