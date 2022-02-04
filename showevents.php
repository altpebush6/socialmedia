<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";
require_once "functions/time.php";

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

$result = array();

$id = security("id");
$clubid = security("part");
$memberid = $SS->get("MemberID");

$allevents = $db->getDatas("SELECT * FROM clubevents WHERE EventID < $id AND EventClubID = ? ORDER BY EventID DESC LIMIT 3", array($clubid));

$counter = count($allevents);

sleep(0.5);

if ($counter > 0) {
    foreach ($allevents as $event) {
        $eventCreatorID = $event->EventCreatorID;
        $eventID = $event->EventID;
        $creatorNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($eventCreatorID));
        $event_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($eventCreatorID));
        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($eventCreatorID));

        $isPersonActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($eventCreatorID));
        if ($isPersonActive != 1) {
            $event_profile_photo = NULL;
            $creatorNames = $translates["unknownuser"];
        }

        if (is_null($event_profile_photo)) {
            if ($gender == 'Male') {
                $event_profile_photo = "profilemale.png";
            } else {
                $event_profile_photo = "profilefemale.png";
            }
        }
        $result["state"] .= '<div class="container p-0 m-0 my-4 px-4 event_' . $eventID . '" id="' . $eventID . '">
                                                <div class="create-post border rounded-1 col-md-10 mx-auto p-4 shadow">
                                                    <div class="row d-flex justify-content-center">
                                                        <div class="col-3 col-md-1 m-0 p-0 ps-md-2">
                                                            <img src="images_profile/' . $event_profile_photo . '" class="rounded-circle" style="width: 60px;height:60px;">
                                                        </div>
                                                        <div class="col-5 d-flex d-md-none align-items-center m-0 p-0">
                                                            <h4><b>' . $event->EventTopic . '</b></h4>
                                                        </div>
                                                        <div class="col-3 d-flex d-md-none align-items-center mb-3 p-0"><span class="text-dark text-center fs-5">~' . $creatorNames . '</span></div>
                                                        <div class="col-md-11">
                                                            <div class="row">
                                                                <div class="col-12 d-none d-md-flex flex-row justify-content-between">
                                                                    <h4><b>' . $event->EventTopic . '</b></h4>
                                                                    <span class="text-dark fs-5">~' . $creatorNames . '</span>
                                                                </div>
                                                                <div class="col-12">
                                                                    <span class="text-dark"><b>' . $translates["eventdatetime"] . ':</b> ' . $event->EventDateTime . '</span>
                                                                </div>
                                                                <div class="col-12">
                                                                    <span class="text-dark"><b>' . $translates["eventplace2"] . ':</b> ' . $event->EventPlace . '</span>
                                                                </div>
                                                                <div class="col-12">
                                                                    <span class="text-dark"><b>' . $translates["eventfor"] . ':</b> ' . $event->EventFor . '</span>
                                                                </div>';

        if ($eventnote) {
            $result["state"] .= '<div class="col-12">
                                                                        <span class="text-dark"><b>' . $translates["eventnote"] . ':</b> ' . $event->EventNote . '</span>
                                                                    </div>';
        }
        $result["state"] .= '<div class="col-12 text-end my-2 my-md-0">';
        if ($event->EventCreatorID == $memberid) {
            $result["state"] .= '<i class="far fa-trash-alt text-danger opacity-8 me-3 deleteEvent" eventid="' . $eventID . '"></i>';
        }
        $result["state"] .= '<span class="text-dark me-4" id="eventparticipant_' . $eventID . '">' . $translates["eventparticipant"] . ': ' . $event->ParticipantNumber . '</span>';
        $isjoined = $db->getData("SELECT * FROM clubeventparticipants WHERE MemberID = ? AND EventID = ?", array($memberid, $eventID));
        if ($isjoined) {
            $result["state"] .= '<button type="button" class="btn btn-primary canceljoin" eventid="' . $eventID . '" id="canceljoin_' . $eventID . '">' . $translates["canceljoin"] . ' <span class="spinner" id="spinnercanceljoin"></span></button>';
        } else {
            $result["state"] .= '<button type="button" class="btn btn-success joinevent" eventid="' . $eventID . '" id="joinevent_' . $eventID . '">' . $translates["join"] . ' <span class="spinner" id="spinnerjoin"></span></button>';
        }
        $result["state"] .= '</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
    }
}
echo json_encode($result);
