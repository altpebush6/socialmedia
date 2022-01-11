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

switch ($operation) {

    case 'searchClubs':
        $searched_key = security("search");
        $searched_clubs = $db->getDatas("SELECT * FROM clubs WHERE ClubState = ? AND ClubName LIKE '$searched_key%' ORDER BY ClubName ASC", array(1));
        foreach ($searched_clubs as $club) {
            $clubname = $club->ClubName;
            if (strlen($clubname) > 18) {
                $clubname = substr($clubname, 0, 18);
                $lastletter = substr($clubname, -1);
                if ($lastletter == " ") {
                    $clubname = substr($clubname, 0, 17);
                }
                $clubname .= "...";
            }
            $result["clubs"] .= '<div class="col-md-3 p-3 m-2" style="border:2px solid rgba(255, 255, 255, 0.281);box-shadow:1px 1px 5px white;">
                                    <div class="row">
                                        <div class="col-3 m-0 p-0 d-flex justify-content-center align-items-center">
                                            <img src="club_images/' . $club->ClubImg . '" class="rounded-circle" style="width:50px;height:50px;border:2px solid rgba(255, 255, 255, 0.788);">
                                        </div>
                                        <div class="col-6 d-flex align-items-center">
                                            <div class="col-12 fs-5 text-light ps-1" title="' . $club->ClubName . '">' . $clubname . '</div>
                                        </div>
                                        <div class="col-3 d-flex align-items-center">
                                            <a href="http://localhost/aybu/socialmedia/' . $translates["clubs"] . '/' . $club->ClubID . '" class="btn btn-outline-dark w-100">' . $translates["go"] . '</a>
                                        </div>
                                    </div>
                                </div>';
        }
        $result["searchedkey"] = $searched_key;
        echo json_encode($result);
        break;

    case 'addclub':
        $clubName = security("clubname");
        if (empty($clubName)) {
            $result["error"] = $translates["errorclubname"];
        } else {
            $clubscope = security("clubscope");
            if ($clubscope == 0) {
                $result["error"] = $translates["chooseclubscope"];
            } else {
                $clubImg = $_FILES['clubimg']['name'];
                if (empty($clubImg)) {
                    $result["error"] = $translates["errorclubimg"];
                } else {
                    if ($clubImg) {
                        $clubImg_ext = strtolower(pathinfo($clubImg, PATHINFO_EXTENSION));
                        $allowed_file_extensions = array("png", "jpg", "jpeg", "jfif");
                        if (!in_array($clubImg_ext, $allowed_file_extensions)) {
                            $result["error"] = "Sadece jpeg, jpg, png ve jfif uzantılı dosya yükleyebilirsiniz.";
                        } else {
                            $clubimgname = $clubName;
                            $clubimgname = preg_replace("/ /", "_", $clubimgname);
                            $clubImg = $clubimgname . "." . $clubImg_ext;
                            $target = "club_images/" . basename($clubImg);
                        }
                        move_uploaded_file($_FILES['clubimg']['tmp_name'], $target);
                    } else {
                        $clubImg = "noneimage.png";
                    }

                    $insertclub = $db->Insert("INSERT INTO clubs SET
                                                ClubName = ?,
                                                ClubImg = ?,
                                                ClubScope = ?,
                                                ClubPresidentID = ?", array($clubName, $clubImg, $clubscope, $memberid));
                    if ($insertclub) {
                        $addClubMember = $db->Insert("INSERT INTO clubmembers SET ClubID = ?,
                                                    MemberID = ?,
                                                    MemberPosition = ?,
                                                    Activeness = ?", array($insertclub, $memberid, "President", 1));
                        $result["success"] = $translates["successaddclub"];
                    }
                }
            }
        }
        echo json_encode($result);
        break;

    case 'sendJoin':
        $clubid = security("ClubID");
        $sendrequest = $db->Insert("INSERT INTO clubmembers SET ClubID = ?, MemberID = ?", array($clubid, $memberid));
        if ($sendrequest) {
            $result["success"] = "ok";
        }
        echo json_encode($result);
        break;

    case 'cancelReq':
        $clubid = security("ClubID");
        $cancelrequest = $db->Delete("DELETE FROM clubmembers WHERE ClubID = ? AND MemberID = ?", array($clubid, $memberid));
        $result["success"] = "ok";
        echo json_encode($result);
        break;

    case 'spamClub':
        $clubid = security("ClubID");
        $spamcounter = $db->getColumnData("SELECT ClubSpam FROM clubs WHERE ClubID = ?", array($clubid));
        $spamcounter = ($spamcounter + 1);
        $spamclub = $db->Update("UPDATE clubs SET ClubSpam = ? WHERE ClubID = ?", array($spamcounter, $clubid));
        $spamclub2 = $db->Insert("INSERT INTO clubspams SET SpammerID = ?, ClubID = ?", array($memberid, $clubid));
        $result["success"] = "ok";
        echo json_encode($result);
        break;

    case 'cancelSpam':
        $clubid = security("ClubID");
        $spamcounter = $db->getColumnData("SELECT ClubSpam FROM clubs WHERE ClubID = ?", array($clubid));
        $spamcounter = ($spamcounter - 1);
        $cancelSpam = $db->Update("UPDATE clubs SET ClubSpam = ? WHERE ClubID = ?", array($spamcounter, $clubid));
        $cancelSpam2 = $db->Delete("DELETE FROM clubspams WHERE SpammerID = ? AND ClubID = ?", array($memberid, $clubid));
        $result["success"] = "ok";
        echo json_encode($result);
        break;

    case 'leaveClub':
        $clubid = security("ClubID");
        $leaveclub = $db->Delete("DELETE FROM clubmembers WHERE ClubID = ? AND MemberID = ?", array($clubid, $memberid));
        $result["success"] = "ok";
        echo json_encode($result);
        break;

    case 'addEvent':
        $clubid = security("ClubID");
        $eventtopic = security("eventtopic");
        $eventdate = security("eventdate");
        $eventtime = security("eventtime");
        $eventdateTime = $eventdate . " " . $eventtime . ":00";
        $eventplace = security("eventplace");
        $eventnote = security("eventnote");
        $eventfor = security("eventfor");
        if (empty($eventtopic) or empty($eventdate) or empty($eventtime) or empty($eventdateTime) or empty($eventfor) or empty($eventplace)) {
            $result["error"] = $translates["empty"];
        } else {
            if (empty($eventnote)) {
                $eventnote = null;
            }
            $clubpresidentID = $db->getColumnData("SELECT ClubPresidentID FROM clubs WHERE ClubID = ?", array($clubid));
            if ($memberid == $clubpresidentID) {
                $insertevent = $db->Insert("INSERT INTO clubevents SET 
                                        EventCreatorID = ?,
                                        EventClubID = ?,
                                        EventTopic = ?,
                                        EventNote = ?,
                                        EventDateTime = ?,
                                        EventPlace = ?,
                                        EventFor = ?", array($memberid, $clubid, $eventtopic, $eventnote, $eventdateTime, $eventplace, $eventfor));
                if ($insertevent) {
                    $result["success"] = $translates["successevent"];
                }
            } else {
                $result["error"] = $translates["notauthorized"];
            }
        }
        echo json_encode($result);
        break;

    case 'joinEvent':
        $clubid = security("ClubID");
        $eventid = security("EventID");
        $participantcounter = $db->getColumnData("SELECT ParticipantNumber FROM clubevents WHERE EventID = ?", array($eventid));
        $participantcounter = ($participantcounter + 1);
        $increaseparticipant = $db->Update("UPDATE clubevents SET ParticipantNumber = ? WHERE EventID = ?", array($participantcounter, $eventid));
        $joinevent = $db->Insert("INSERT INTO clubeventparticipants SET MemberID = ?, EventID = ?, ClubID = ?", array($memberid, $eventid, $clubid));
        $result["success"] = "ok";
        $result["participantnumber"] = $participantcounter;
        echo json_encode($result);
        break;

    case 'cancelJoin':
        $clubid = security("ClubID");
        $eventid = security("EventID");
        $participantcounter = $db->getColumnData("SELECT ParticipantNumber FROM clubevents WHERE EventID = ?", array($eventid));
        $participantcounter = ($participantcounter - 1);
        $decreaseparticipant = $db->Update("UPDATE clubevents SET ParticipantNumber = ? WHERE EventID = ?", array($participantcounter, $eventid));
        $cancelevent = $db->Delete("DELETE FROM clubeventparticipants WHERE MemberID = ? AND EventID = ? ", array($memberid, $eventid));
        $result["success"] = "ok";
        $result["participantnumber"] = $participantcounter;
        echo json_encode($result);
        break;

    case 'acceptRequest':
        $clubid = security("ClubID");
        $MembershipID = security("MembershipID");
        $acceptReq = $db->Update("UPDATE clubmembers SET Activeness = ? WHERE MembershipID = ?", array(1, $MembershipID));
        $anyleft = $db->getColumnData("SELECT COUNT(*) FROM clubmembers WHERE ClubID = ? AND Activeness = ?", array($clubid, 0));
        if (!$anyleft) {
            $result["anyleft"] = "no";
        }
        $newNumber = $db->getColumnData("SELECT COUNT(*) FROM clubmembers WHERE ClubID = ? AND Activeness = ?", array($clubid, 1));
        $result["newNumber"] = $newNumber;
        $memberID = $db->getColumnData("SELECT MemberID FROM clubmembers WHERE MembershipID = ?", array($MembershipID));
        $memberNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($memberID));
        $memberimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberID));
        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($memberID));
        if (is_null($memberimg)) {
            if ($gender == 'Male') {
                $memberimg = "profilefullmale.jpg";
            } else {
                $memberimg = "profilefullfemale.jpg";
            }
        }
        $result["carouselitem"] = '<div class="d-flex flex-column justify-content-between mx-1 item carousel-div text-center friend-box" style="background-image: url(\'images_profile/' . $memberimg . '\');" id="clubMember_' . $MemberID . '">
                                        <div class="row justify-content-end">
                                            <button class="col-3 m-0 p-0 text-center bg-light text-danger d-flex justify-content-center align-items-center rounded-circle removeMemberDiv me-2 opt_dropdown dropbtn" memid="' . $MemberID . '"><i class="fas fa-ellipsis-h"></i></button>
                                            <div class="dropdown-content rounded-2 mt-4 px-0" style="display:none;width:270px;" id="opt_dropbox_' . $MemberID . '">
                                                <a href="javascript:void(0)" class="w-100 px-0"><i class="fas fa-angle-double-up text-success" clubmemberid="' . $MemberID . '"></i> ' . $translates["promotetoman"] . ' </a>
                                                <a href="javascript:void(0) removeMember" class="w-100 px-0"><i class="fas fa-user-slash text-danger" clubmemberid="' . $MemberID . '"></i> ' . $translates["removefromclub"] . '</a>
                                            </div>
                                        </div>
                                        <a class="d-flex flex-column text-center mt-auto p-3 bg-dark text-light fs-5 rounded-3 text-decoration-none" href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $MemberID . '">
                                            <span>' . $memberNames . '</span>
                                        </a>
                                    </div>';
        echo json_encode($result);
        break;

    case 'refuseRequest':
        $clubid = security("ClubID");
        $MembershipID = security("MembershipID");
        $refuseReq = $db->Delete("DELETE FROM  clubmembers WHERE MembershipID = ?", array($MembershipID));
        $anyleft = $db->getColumnData("SELECT COUNT(*) FROM clubmembers WHERE ClubID = ? AND Activeness = ?", array($clubid, 0));
        if (!$anyleft) {
            $result["anyleft"] = "no";
        }
        echo json_encode($result);
        break;

    case 'removeMember':
        $clubid = security("ClubID");
        $ClubMemberID = security("ClubMemberID");
        $deleteMember = $db->Delete("DELETE FROM clubmembers WHERE ClubID = ? AND MemberID = ?", array($clubid, $ClubMemberID));
        $newNumber = $db->getColumnData("SELECT COUNT(*) FROM clubmembers WHERE ClubID = ? AND Activeness = ?", array($clubid, 1));
        $result["newNumber"] = $newNumber;
        echo json_encode($result);
        break;
    case 'promoteMember':
        $clubid = security("ClubID");
        $ClubMemberID = security("ClubMemberID");
        $promoteMember = $db->Update("UPDATE clubmembers SET MemberPosition = ? WHERE ClubID = ? AND MemberID = ?", array('Management', $clubid, $ClubMemberID));
        $memberimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($ClubMemberID));
        $memberNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($ClubMemberID));
        $result["member"] = '<div class="d-flex flex-column justify-content-between mx-1 item carousel-div text-center friend-box" style="background-image: url(\'images_profile/' . $memberimg . '\');" id="clubMember_' . $ClubMemberID . '">
                                 <div class="row justify-content-end">
                                    <button class="col-3 m-0 p-0 text-center text-dark d-flex justify-content-center align-items-center rounded-circle removeMemberDiv me-2 opt_dropdown dropbtn" memid="' . $ClubMemberID . '"><i class="fas fa-ellipsis-h"></i></button>
                                    <div class="dropdown-content rounded-2 mt-4 px-0" style="display:none;width:280px;font-size:15px" id="opt_dropbox_' . $ClubMemberID . '">
                                        <a href="javascript:void(0)" class="w-100 px-0 deductMember" clubmemberid="' . $ClubMemberID . '"><i class="fas fa-angle-double-down text-danger"></i> ' . $translates["deduct"] . '</a>
                                        <a href="javascript:void(0)" class="w-100 px-0 removeMember" clubmemberid="' . $ClubMemberID . '"><i class="fas fa-user-slash text-danger"></i> ' . $translates["removefromclub"] . '</a>
                                    </div>
                                </div>
                                <a class="d-flex flex-column text-center mt-auto p-3 bg-dark text-light fs-5 rounded-3 text-decoration-none" href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $ClubMemberID . '">
                                    <span>' . $memberNames . '</span>
                                </a>
                            </div>';
        echo json_encode($result);
        break;
    case 'deductMember':
        $clubid = security("ClubID");
        $ClubMemberID = security("ClubMemberID");
        $deductMember = $db->Update("UPDATE clubmembers SET MemberPosition = ? WHERE ClubID = ? AND MemberID = ?", array('Member', $clubid, $ClubMemberID));
        $memberimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($ClubMemberID));
        $memberNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($ClubMemberID));
        $result["member"] = '<div class="d-flex flex-column justify-content-between mx-1 item carousel-div text-center friend-box" style="background-image: url(\'images_profile/'. $memberimg .'\');" id="clubMember_'. $ClubMemberID .'">
                                <div class="row justify-content-end">
                                    <button class="col-3 m-0 p-0 text-center text-dark d-flex justify-content-center align-items-center rounded-circle removeMemberDiv me-2 opt_dropdown dropbtn" memid="'. $ClubMemberID .'"><i class="fas fa-ellipsis-h"></i></button>
                                    <div class="dropdown-content rounded-2 mt-4 px-0" style="display:none;width:280px;font-size:15px" id="opt_dropbox_'. $ClubMemberID .'">
                                        <a href="javascript:void(0)" class="w-100 px-0 promoteMember" clubmemberid="'. $ClubMemberID .'"><i class="fas fa-angle-double-up text-success"></i> '. $translates["promotetoman"] .'</a>
                                        <a href="javascript:void(0)" class="w-100 px-0 removeMember" clubmemberid="'. $ClubMemberID .'"><i class="fas fa-user-slash text-danger"></i> '. $translates["removefromclub"] .'</a>
                                    </div>
                                </div>
                                <a class="d-flex flex-column text-center mt-auto p-3 bg-dark text-light fs-5 rounded-3 text-decoration-none" href="http://localhost/aybu/socialmedia/'. $translates['profile'] .'/'. $ClubMemberID .'">
                                    <span>'. $memberNames .'</span>
                                </a>
                            </div>';
        echo json_encode($result);
        break;
}
