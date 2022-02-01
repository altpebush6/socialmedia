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
$topic = security("part");
$memberid = $SS->get("MemberID");

if ($topic == $translates["love"]) {
    $topic = "AND ConfessionTopic = '0'";
} else if ($topic == $translates["anger"]) {
    $topic = "AND ConfessionTopic = '1'";
}else{
    $topic = "";
}

$allcnfn = $db->getDatas("SELECT * FROM confessions WHERE ConfessionID < $id $topic ORDER BY ConfessionAddTime DESC LIMIT 3");

$counter = count($allcnfn);

sleep(0.5);

if ($counter > 0) {
    foreach ($allcnfn as $confession) {
        $cnfnMemberID = $confession->MemberID;
        $visibility = $confession->ConfessionVisibility;
        $topic = $confession->ConfessionTopic;
        $cnfnID = $confession->ConfessionID;
        $MemberNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($cnfnMemberID));
        $cnfn_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = $cnfnMemberID");
        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($cnfnMemberID));
        $isConffessionownerActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($cnfnMemberID));
        if ($isConffessionownerActive == 1) {
            if ($gender == 'Male') {
                if (is_null($cnfn_profile_photo)) {
                    $cnfn_profile_photo = "profilemale.png";
                }
                $cnfn_anonym_photo = "profilemale.png";
            } else {
                if (is_null($cnfn_profile_photo)) {
                    $cnfn_profile_photo = "profilefemale.png";
                }
                $cnfn_anonym_photo = "profilefemale.png";
            }
            $diff_cnfn = calculateTime($confession->ConfessionAddTime);

            $result["state"] .= '<div class="container col-md-7 mx-auto p-0 my-4" id="' . $cnfnID . '">
            <div class="border p-3 col-md-12 m-0 py-4 post bg-light shadow" style="border-radius: 15px;">
                <div class="row mb-3">
                    <div class="col-10">
                        <a ';
            if ($visibility) {
                $result["state"] .= 'href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $cnfnMemberID . '"';
            }
            $result["state"] .= '><div class="row justify-content-center">
                                <div class="col-2 text-end">
                                <a ';
            if ($visibility) {
                $result["state"] .= 'href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $cnfnMemberID . '"';
            }
            $result["state"] .= '><img src="images_profile/';
            if ($visibility) {
                $result["state"] .= $cnfn_profile_photo;
            } else {
                $result["state"] .= $cnfn_anonym_photo;
            }
            $result["state"] .= '" class="rounded-circle" width="50" height="50">
                                    </a>
                                </div>
                                <div class="col-10 ps-3 p-md-0 ">
                                <a class="text-decoration-none text-dark"';
            if ($visibility) {
                $result["state"] .= 'href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $cnfnMemberID . '"';
            }
            $result["state"] .= '>';
            if ($visibility) {
                $result["state"] .= $MemberNames;
            } else {
                $result["state"] .= $translates["anonymous"];
            }
            $result["state"] .= '<br><small>' . $diff_cnfn . '</small>
                                    </a>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-2">
                        <div class="dropdown-post">
                            <button class="dropbtn btn rounded-circle btn-post"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-content" style="width:220px;">';
            if ($confession->MemberID == $memberid) {
                $result["state"] .= '<a href="javascript:void(0)" onClick="OpenEditConfession(\'' . $cnfnID . '\',\'' . $confession->ConfessionText . '\')"><i class="far fa-edit"></i> ' . $translates["editcnfn"] . '</a>
                                    <a href="javascript:void(0)" onClick="DeleteConfession(\'deleteconfession\',\'' . $memberid . '\',\'' . $cnfnID . '\')"><i class="far fa-trash-alt"> ' . $translates["deletecnfn"] . '</i></a>';
            } else {
                $diduRep = $db->getData("SELECT * FROM reports_cnfn WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $cnfnID));
                if ($diduRep) {
                    $result["state"] .= '<a href="javascript:void(0)" class="text-success unreportCnfn" postid="' . $cnfnID . '" id="Report_Cnfn_' . $cnfnID . '"><i class="fas fa-headset"></i> ' . $translates["reportedcnfn"] . '</a>';
                } else {
                    $result["state"] .= '<a href="javascript:void(0)" class="text-danger reportCnfn" postid="' . $cnfnID . '" id="Report_Cnfn_' . $cnfnID . '"><i class="fas fa-bug"></i> ' . $translates["reportcnfn"] . '</a>';
                }
            }
            $result["state"] .= '</div>
                        </div>
                    </div>
                </div>
                <div class="text-break fs-6 cnfnmiddle_' . $cnfnID . '" style="user-select:text" id="cnfnmiddle_' . $cnfnID . '">
                    <span id="cnfn_text_' . $cnfnID . '" class="ps-4 my-3">' . $confession->ConfessionText . '</span>
                </div>
                <!-- İtiraf Düzenleme -->
                <div class="d-none" id="editCnfn_' . $cnfnID . '">
                    <form id="form_editCnfn_' . $cnfnID . '" class="form_edit" method="post" enctype="multipart/form-data">
                        <div class="col-10 mx-auto my-3">
                            <input autocomplete="off" type="text" class="form-control-plaintext" name="edittedtext" id="edittedtext_' . $cnfnID . '" value="' . $confession->ConfessionText . '">
                        </div>
                        <div class="col-10 mx-auto mt-2">
                            <div class="row">
                                <div class="col-8">
                                    <select name="edittedVisibilityOpt" id="edittedVisibilityOpt_' . $cnfnID . '" class="form-select form-select-sm">
                                        <option value="0"';
            if (!$visibility) {
                $result["state"] .= 'selected';
            }
            $result["state"] .= '>' . $translates["anonymous"] . '</option>
                                        <option value="1" ';
            if ($visibility) {
                $result["state"] .= 'selected';
            }
            $result["state"] .= '>' . $translates["withyourprofile"] . '</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select name="edittedTopicOpt" id="edittedTopicOpt_' . $cnfnID . '" class="form-select form-select-sm">
                                        <option value="0" ';
            if ($topic) {
                $result["state"] .= 'selected';
            }
            $result["state"] .= '>' . $translates["Love"] . '</option>
                                        <option value="1"';
            if ($topic) {
                $result["state"] .= 'selected';
            }
            $result["state"] .= '>' . $translates["Anger"] . '</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-10 mx-auto">
                                <button type="submit" class="btn w-100 rounded-3 border fs-5 saveedit shadow btn-post" name="saveedit" idsi="' . $cnfnID . '" id="saveedit_' . $cnfnID . '">' . $translates["shareit"] . ' <span class="spinner" id="spinnercnfnedit"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
        }
    }
}
echo json_encode($result);
