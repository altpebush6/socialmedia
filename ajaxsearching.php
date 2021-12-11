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

    if ($_POST["search"]) {
      $searched_key = $_POST["search"];
    }
    $output["total"] = $db->getColumnData("SELECT COUNT(*) FROM members WHERE MemberNames LIKE '$searched_key%'");
    $member_searched = $db->getDatas("SELECT * FROM members WHERE MemberNames LIKE '$searched_key%' LIMIT 2");

    foreach ($member_searched as $item) {
      if ($item->MemberConfirm == 1) {
        $person_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($item->MemberID));
        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($item->MemberID));
        if (is_null($person_photo)) {
          if ($gender == 'Erkek') {
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
    if ($_POST["search"]) {
      $searched_key = $_POST["search"];
      $member_searched = $db->getDatas("SELECT * FROM members WHERE MemberNames LIKE '$searched_key%'");
      foreach ($member_searched as $item) {
        if ($item->MemberConfirm == 1) {
          $personID = $item->MemberID;
          $name_lastname = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
          $person_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
          $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
          if (is_null($person_photo)) {
            if ($gender == 'Erkek') {
              $person_photo = "profilemale.png";
            } else {
              $person_photo = "profilefemale.png";
            }
          }
          $ishaveMessage = $db->getData("SELECT * FROM chatbox
                                              WHERE MessageStatus = 1 AND ((MessageFromID = $memberid AND MessageToID = $personID) OR (MessageFromID = $personID AND MessageToID = $memberid))");
          if ($ishaveMessage) {
            $messageID = $db->GetColumnData("SELECT MessageID FROM messages
                                                  WHERE MessageStatus = 1 AND ((MessageFromID = $memberid AND MessageToID = $personID) OR (MessageFromID = $personID AND MessageToID = $memberid))
                                                  ORDER BY MessageAddTime DESC");

            $messageText = $db->getColumnData("SELECT MessageText FROM messages WHERE MessageID = ?", array($messageID));

            $whosemessage =  $db->GetColumnData("SELECT MessageFromID FROM messages WHERE MessageID = ?", array($messageID));

            $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages
                                                    WHERE MessageID = ? AND MessageStatus = ?", array($messageID, 1));


            if ($whosemessage != $memberid) {
              $messageHasRead = $db->getColumnData("SELECT MessageHasRead FROM chatbox
                    WHERE MessageStatus = 1 AND ((MessageFromID = $memberid AND MessageToID = $personID) OR (MessageFromID = $personID AND MessageToID = $memberid))");
            } else {
              $messageHasRead = 1;
            }

            if ($whosemessage == $memberid) {
              $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($messageID));
              if ($messageHasSeen == 1) {
                $tic = '<i class="fas fa-check-double" style="font-size:12px;color:blue"></i>';
              } else {
                $tic = '<i class="fas fa-check" style="font-size:12px;"></i>';
              }
              $fromwho = $translates["you"];
            } else {
              $tic = '';
              $fromwho = $item->MemberName;
            }

            $messageImg = $db->getColumnData("SELECT MessageImg FROM messages WHERE MessageID = ?", array($messageID));
            $shortcutphoto = '<i class="fas fa-camera"></i> ' . $translates["photo"];
            $messageText = ($messageImg ? $shortcutphoto : $messageText);

            $resultcontent = $fromwho . ': ' . $messageText . ' ' . $tic;

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

            if ($messageHasRead == 0 and $personID != $part) {
              $styletext = "style='opacity:1;'";
            } else {
              $styletext =  "style='opacity:0.5'";
            }
          } else {
            $resultcontent = '';
          }


          $output["data"] .= '<a class="text-light text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '">
                                <div class="row my-2 justify-content-center align-items-center">
                                  <div class="col-2 text-center">
                                    <img src="images_profile/' . $person_photo . '" class="rounded-circle" width="60" height="60">
                                  </div>
                                  <div class="col-8 px-3 ps-md-5 ps-lg-4 ps-xl-5 d-flex flex-column">
                                    <div class="row fs-5" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">' . $name_lastname . '</div>
                                    <div class="row">
                                      <div class="col-12 p-0 text-start person-content" id="content_' . $personID . '" ' . $styletext . '>
                                      ' . $resultcontent . '
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-2 text-center d-flex flex-column justify-content-between">
                                    <div class="row"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                    <div class="row" id="chatpersontime_' . $personID . '"><small>' . messageTime($messagetime) . '</small></div>
                                  </div>
                                </div>';
          $output["deneme"] = $searched_key;
        }
      }
    } else {
      $chatpersons = $db->getDatas("SELECT * FROM chatbox
                                        WHERE MessageStatus = 1 AND (MessageFromID = $memberid OR MessageToID = $memberid)
                                        ORDER BY LastTime DESC");
      foreach ($chatpersons as $info) {
        if ($info->MessageFromID == $memberid) {
          $personID = $info->MessageToID;
        } else {
          $personID = $info->MessageFromID;
        }
        $getprofileimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
        $ChatPersonName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($personID));
        $ChatPersonLastName  = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = ?", array($personID));
        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
        if (is_null($getprofileimg)) {
          if ($gender == 'Erkek') {
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

        $whosemessage =  $db->GetColumnData("SELECT MessageFromID FROM messages WHERE MessageID = ?", array($messageID));

        $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages
                                              WHERE MessageID = ? AND MessageStatus = ?", array($messageID, 1));
        $diff_message = calculateTime($messagetime);

        if ($whosemessage != $memberid) {
          $messageHasRead = $db->getColumnData("SELECT MessageHasRead FROM chatbox
                WHERE MessageStatus = 1 AND ((MessageFromID = $memberid AND MessageToID = $personID) OR (MessageFromID = $personID AND MessageToID = $memberid))");
        } else {
          $messageHasRead = 1;
        }

        if ($messageHasRead == 0 and $personID != $part) {
          $styleperson = "style='opacity:1;'";
        } else {
          $styleperson =  "style='opacity:0.5'";
        }

        if ($personID == $part) {
          $style = "style='background:rgba(255, 255, 255, 0.2)'";
        } else {
          $style = "style=''";
        }
        if ($whosemessage == $memberid) {
          $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($messageID));
          if ($messageHasSeen == 1) {
            $tic = '<i class="fas fa-check-double" style="font-size:12px;color:blue"></i>';
          } else {
            $tic = '<i class="fas fa-check" style="font-size:12px;"></i>';
          }
          $fromwho = $translates["you"];
        } else {
          $fromwho = $ChatPersonName;
          $tic = '';
        }
        $messageImg = $db->getColumnData("SELECT MessageImg FROM messages WHERE MessageID = ?", array($messageID));
        $shortcutphoto = '<i class="fas fa-camera"></i> ' . $translates["photo"];
        $messageText = ($messageImg ? $shortcutphoto : $messageText);
        $resultcontent = $fromwho . ': ' . $messageText . ' ' . $tic;
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
        $output["null"] .= '<a class="text-light text-decoration-none" id="person_' . $personID . '" href="http://localhost/aybu/socialmedia/' . $translates['messages'] . '/' . $personID . '">
                              <div class="row my-2 justify-content-center align-items-center">
                                <div class="col-2 text-center">
                                  <img src="images_profile/' . $getprofileimg . '" class="rounded-circle" width="60" height="60">
                                </div>
                                <div class="col-8 px-3 ps-md-5 ps-lg-4 ps-xl-5">
                                  <div class="row fs-5" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">' . $name_lastname . '</div>
                                  <div class="row">
                                    <div class="col-12 p-0 text-start person-content" id="content_' . $personID . '" ' . $styleperson . '>
                                    ' . $resultcontent . '
                                    </div>
                                  </div>
                                </div>
                                <div class="col-2 text-center d-flex flex-column justify-content-between">
                                  <div class="row"><i class="fas fa-circle offline" id="chatperson_' . $personID . '" ' . $styleicon . '></i></div>
                                  <div class="row" id="chatpersontime_' . $personID . '"><small>' . messageTime($messagetime) . '</small></div>
                                </div>
                              </div>
                            </a>';
      }
    }
    echo json_encode($output);
    break;
}
