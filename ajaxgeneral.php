<?php
if (!isset($_SESSION)) {
    session_start();
  }
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";

$db = new aybu\db\mysqlDB();
$SS = new aybu\session\session();

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}
require_once "languages/language_" . $language . ".php";

$operation = $_GET['operation'];
$result = array();

switch ($operation) {
    case 'editchatbox':
        $ChatboxID = $_GET['ChatboxID'];
        $FromID = security("form_FromID");
        $ToID = security("form_ToID");
        $MessageStatus = security("form_MessageStatus");

        $updatedb = $db->Update("UPDATE chatbox
                                SET MessageFromID = ?,
                                MessageToID = ?,
                                MessageStatus = ? WHERE ChatboxID = ?", array($FromID, $ToID, $MessageStatus, $ChatboxID));


        $result["MessageStatus"] = $MessageStatus;
        $result["ToID"] = $ToID;
        $result["FromID"] = $FromID;

        echo json_encode($result);
        break;
    case 'delchatbox':
        $ChatboxID = security("ChatboxID");
        $delete = $db->Update("UPDATE chatbox SET MessageStatus = ? WHERE ChatboxID = ?", array(0, $ChatboxID));
        $result["success"] = "ok";
        echo json_encode($result);
        break;

    case 'editmessage':
        $MessageID = $_GET['MessageID'];
        $MessageText = security("form_MessageText");
        $MessageImg = security("form_MessageImg");
        $MessageFromID = security("form_MessageFromID");
        $MessageToID = security("form_MessageToID");
        $MessageStatus = security("form_MessageStatus");
        if ($MessageImg == "null" or $MessageImg == "") {
            $MessageImg = null;
        }

        $updatedb = $db->Update("UPDATE messages
                                SET MessageText = ?,
                                MessageImg = ?,
                                MessageFromID = ?,
                                MessageToID = ?,
                                MessageStatus = ? WHERE MessageID = ?", array($MessageText, $MessageImg, $MessageFromID, $MessageToID, $MessageStatus, $MessageID));


        if ($MessageImg == null) {
            $MessageImg = "null";
        }
        $result["MessageID"] = $MessageID;
        $result["MessageText"] = $MessageText;
        $result["MessageImg"] = $MessageImg;
        $result["MessageFromID"] = $MessageFromID;
        $result["MessageToID"] = $MessageToID;
        $result["MessageStatus"] = $MessageStatus;


        echo json_encode($result);
        break;
    case 'delmessage':
        $MessageID = security("MessageID");
        $delete = $db->Update("UPDATE messages SET MessageStatus = ? WHERE MessageID = ?", array(0, $MessageID));
        $result["success"] = "ok";
        echo json_encode($result);
        break;

    case 'editimage':
        $ImgID = $_GET["ImgID"];
        $profileImg = security("form_profileimg");
        $coverImg = security("form_coverimg");
        if ($profileImg == "null" or $profileImg == "") {
            $profileImg = null;
        }
        if ($coverImg == "null" or $coverImg == "") {
            $coverImg = null;
        }

        $updatedb = $db->Update("UPDATE images
                                SET Member_Profileimg = ?,
                                Member_Coverimg = ? WHERE imgID = ?", array($profileImg, $coverImg, $ImgID));


        if ($profileImg == null) {
            $profileImg = "null";
        }
        if ($coverImg == null) {
            $coverImg = "null";
        }

        $result["ImgID"] = $ImgID;
        $result["ProfileImg"] = $profileImg;
        $result["CoverImg"] = $coverImg;

        echo json_encode($result);
        break;

    case 'editabout':
        $AboutID = $_GET["AboutID"];
        $Faculty = security("form_Faculty");
        $Department = security("form_Department");
        $Hobbies = security("form_Hobbies");
        $FavTV = security("form_FavTV");
        $Hometown = security("form_Hometown");
        $City = security("form_City");

        $departmentID = $db->getDatas("SELECT * FROM departments_en WHERE FacultyID = ?", array($Faculty));

        foreach ($departmentID as $departmentitem) {
            $departmentsID = array();
            array_push($departmentsID, $departmentitem->DepartmentID);
        }
        if (!in_array($Department, $departmentsID)) {
            $Department = null;
        }




        $updatedb = $db->Update("UPDATE memberabout
                                SET MemberFaculty = ?,
                                MemberDepartment = ?,
                                MemberHobbies = ?,
                                MemberFavTV = ?,
                                MemberHometown = ?,
                                MemberCity = ? WHERE AboutID = ?", array($Faculty, $Department, $Hobbies, $FavTV, $Hometown, $City, $AboutID));


        $result["AboutID"] = $AboutID;
        $result["Faculty"] = $Faculty;
        $result["Department"] = $Department;
        $result["Hobbies"] = $Hobbies;
        $result["FavTV"] = $FavTV;
        $result["Hometown"] = $Hometown;
        $result["City"] = $City;

        echo json_encode($result);
        break;

    case 'editRequest':
        $RequestID = $_GET["RequestID"];
        $MemberID = security("form_MemberID");
        $RequestItem = security("form_RequestItem");
        $RequestStatus = security("form_RequestStatus");

        $updatedb = $db->Update("UPDATE memberaboutrequests
                                SET MemberID = ?,
                                RequestItem = ?,
                                RequestStatus = ? WHERE RequestID = ?", array($MemberID, $RequestItem, $RequestStatus, $RequestID));


        $result["MemberID"] = $MemberID;
        $result["RequestItem"] = $RequestItem;
        $result["RequestStatus"] = $RequestStatus;

        echo json_encode($result);
        break;

    case 'delRequest':
        $RequestID = security("RequestID");
        $deleteReq = $db->Delete("DELETE FROM memberaboutrequests WHERE RequestID = ?", array($RequestID));
        $result["success"] = "ok";
        echo json_encode($result);
        break;
}
