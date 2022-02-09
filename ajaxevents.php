<?php
if (!isset($_SESSION)) {
    session_start();
}
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

$operation = $_GET['operation'];
$result = array();

switch ($operation) {

    case 'searchSchool':
        $uni = security("uni");
        $searched_key = security("search");
        $searched_schools = $db->getDatas("SELECT * FROM universities WHERE UniversityName LIKE '$searched_key%' ORDER BY UniversityID ASC");
        foreach ($searched_schools as $university) {
            $uniname = $university->UniversityName;
            $ischecked = "";
            $pattern1 = "/-/";
            if (preg_match($pattern1, $uni)) {
                $unis = explode("-", $uni);
                if (in_array($university->UniversityID, $unis)) {
                    $ischecked = "checked";
                }
            } else {
                if ($uni == $university->UniversityID) {
                    $ischecked = "checked";
                }
            }
            $result["schools"] .= '<li class="list-group-item bg-transparent text-light m-0 p-2">
                                    <div class="row pe-3">
                                        <div class="col-2">
                                            <input type="checkbox" class="form-check-input me-4 selectuni" id="' . $university->UniversityID . '" ' . $ischecked . ' style="cursor:pointer;">
                                        </div>
                                        <div class="col-10" style=" white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                            <label for="' . $university->UniversityID . '" title="' . $university->UniversityName . '" style="cursor:pointer;">' . $university->UniversityName . '</label>
                                        </div>
                                    </div>
                                </li>';
        }
        echo json_encode($result);
        break;

    case 'eject':
        $uni = security("uni");
        $uniID = security("uniID");
        $unis = explode("-", $uni);
        foreach ($unis as $key => $value) {
            if ($value == $uniID) {
                unset($unis[$key]);
            }
        }
        $uni = $unis[0];
        foreach ($unis as $uniID) {
            if ($uniID != $unis[0]) {
                $uni .= "-" . $uniID;
            }
        }
        $result["uni"] = $uni;
        echo json_encode($result);
        break;

    case "editEvent":
    case "createEvent":
        $eventHeader = $_POST["eventHeader"];
        $eventCategory = security("eventCategory");
        $explanation = security("explanation");
        $eventSchool = security("eventSchool");
        $eventCity = security("eventCity");
        $noCity = security("noCity");
        $eventPlace = security("eventPlace");
        $eventDate = security("eventDate");
        $emailAddress = security("emailAddress");
        $phoneNum = security("phoneNum");
        $pricing = security("pricing");
        $free = security("free");
        $eventID = security("eventID");
        $eventImage = $_FILES['eventImg']['name'];

        if ($noCity == "true") {
            if ($eventPlace) {
                $status = "notempty";
            } else {
                $status = "itsempty";
            }
        } else {
            if ($eventCity == "null") {
                $status = "itsempty";
            } else {
                $status = "notempty";
            }
        }

        if ($free == "true") {
            $pricing = "0";
            $pricingStatus = "notempty";
        } else {
            if (empty($pricing)) {
                $pricingStatus = "itsempty";
            } else {
                $pricingStatus = "notempty";
            }
        }


        if (empty($eventHeader) or empty($eventImage) or empty($eventCategory) or empty($eventDate) or empty($emailAddress) or empty($phoneNum)) {
            $result["error"] = $translates["emptyareas"];
        } else {
            if ($status == "itsempty" || $pricingStatus == "itsempty") {
                $result["error"] = $translates["emptyareas"];
            } else {
                $emailpattern = "/^[a-zA-Z0-9\.\-\_]+@[a-z]+([a-zA-Z0-9\.]+)?\.([a-z]{2,})([a-zA-Z0-9\.]+)?$/";
                if (!preg_match($emailpattern, $emailAddress)) {
                    $result["error"] = $translates["invalidemail"];
                } else {
                    $phonepattern = "/[^0-9 ]/";
                    if (preg_match($phonepattern, $phoneNum)) {
                        $result["error"] =  $translates["onlynumber"];
                    } else {
                        $pattern2 = "/\s+/";
                        $replace = "";
                        $phoneNum = preg_replace($pattern2, $replace, $phoneNum);
                        $phoneNum = trim($phoneNum);
                        if (!(strlen($phoneNum) == 10 || strlen($phoneNum) == 11)) {
                            $result["error"] = $translates["numberlength"];
                        } else {
                            $pattern3 = "/^ ?0?\s?5[0-9]{2}\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}\s?$/";
                            if (!preg_match($pattern3, $phoneNum)) {
                                $result["error"] = $translates["invalidnumber"];
                            } else {
                                $eventImage_ext = strtolower(pathinfo($eventImage, PATHINFO_EXTENSION));
                                $allowed_file_extensions = array("png", "jpg", "jpeg", "jfif");
                                if (!in_array($eventImage_ext, $allowed_file_extensions)) {
                                    $result["error"] = $translates["notallowedimg"];
                                } else {
                                    $eventImagename = preg_replace("/ /", "_", $eventHeader);
                                    $eventImagename = preg_replace("/'!\"#\$%&'(),-.\/:;<=>?@\[\]\\^_{|}~/", "_", $eventImagename);
                                    $eventImage = $eventImagename . "_" . uniqid() . "." . $eventImage_ext;
                                    $target = "events_images/" . basename($eventImage);
                                }
                                move_uploaded_file($_FILES['eventImg']['tmp_name'], $target);

                                if ($operation == 'createEvent') {
                                    $createEvent = $db->Insert("INSERT INTO events SET
                                    EventHeader = ?,
                                    EventImage = ?,
                                    EventCategory = ?,
                                    EventSchool = ?,
                                    EventCity = ?,
                                    EventPlace = ?,
                                    EventDateTime = ?,
                                    EventParticipant = ?,
                                    EventPrice = ?,
                                    EventExplanation = ?,
                                    EventOrganizer = ?,
                                    OrganizerEmail = ?,
                                    OrganizerPhone = ?,
                                    EventStatus = ?", array($eventHeader, $eventImage, $eventCategory, $eventSchool, $eventCity, $eventPlace, $eventDate, 0, $pricing, $explanation, $memberid, $emailAddress, $phoneNum, 0));
                                    $result["success"] = $translates["eventhascreated"];
                                } else if ($operation == 'editEvent') {
                                    $editEvent = $db->Update("UPDATE events SET
                                    EventHeader = ?,
                                    EventImage = ?,
                                    EventCategory = ?,
                                    EventSchool = ?,
                                    EventCity = ?,
                                    EventPlace = ?,
                                    EventDateTime = ?,
                                    EventPrice = ?,
                                    EventExplanation = ?,
                                    OrganizerEmail = ?,
                                    OrganizerPhone = ? WHERE EventID = ?", array($eventHeader, $eventImage, $eventCategory, $eventSchool, $eventCity, $eventPlace, $eventDate, $pricing, $explanation, $emailAddress, $phoneNum, $eventID));
                                    $result["success"] = $translates["eventhasedited"];
                                    $result["newlink"] = seolink($eventHeader) . "-" . $eventID;
                                }
                            } //if (!preg_match($pattern3, $phoneNum))
                        } //if (!(strlen($phoneNum) == 10 || strlen($phoneNum) == 11))
                    } // if (preg_match($phonepattern, $phoneNum))
                } // if (!preg_match($emailpattern, $emailAddress))
            } // if ($status == "itsempty")
        } // if (empty($eventHeader) or empty($eventCategory) or empty($eventDate) or empty($emailAddress) or empty($phoneNum) or empty($pricing))

        echo json_encode($result);
        break;

    case 'joinEvent':
        $EventID = security("EventID");
        $insertparticipant = $db->Insert("INSERT INTO eventparticipants SET MemberID = ?, EventID = ?", array($memberid, $EventID));
        $preParticipantNum = $db->getColumnData("SELECT EventParticipant FROM events WHERE EventID = ?", array($EventID));
        $newParticipantNum = $preParticipantNum + 1;
        $increaseNum = $db->Update("UPDATE events SET EventParticipant = ? WHERE EventID = ?", array($newParticipantNum, $EventID));
        $result["newNumber"] = $newParticipantNum;
        echo json_encode($result);
        break;

    case 'cancelJoin':
        $EventID = security("EventID");
        $deleteparticipant = $db->Delete("DELETE FROM eventparticipants WHERE MemberID = ? AND EventID = ?", array($memberid, $EventID));
        $preParticipantNum = $db->getColumnData("SELECT EventParticipant FROM events WHERE EventID = ?", array($EventID));
        $newParticipantNum = $preParticipantNum - 1;
        $decreaseNum = $db->Update("UPDATE events SET EventParticipant = ? WHERE EventID = ?", array($newParticipantNum, $EventID));
        $result["newNumber"] = $newParticipantNum;
        echo json_encode($result);
        break;

    case 'getPremium':
        $EventID = security("EventID");
        $getPremium = $db->Update("UPDATE events SET EventPremium = ? WHERE EventID = ?", array(1, $EventID));
        echo json_encode($result);
        break;
}
