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
    case 'filterCourse':
        $CourseName = security("CourseName");
        $CourseCode = security("CourseCode");
        $CourseClass = security("CourseClass");
        if ($CourseClass == "") {
            $courses = $db->getDatas("SELECT * FROM courses WHERE CourseName LIKE '$CourseName%' AND CourseCode LIKE '$CourseCode%'");
        } else {
            $courses = $db->getDatas("SELECT * FROM courses WHERE CourseName LIKE '$CourseName%' AND CourseCode LIKE '$CourseCode%' AND CourseClass = '$CourseClass%'");
        }

        if ($courses) {
            foreach ($courses as $course) {
                $result["course"] .= '<a class="btn btn-post shadow mx-1 mt-3" href="http://localhost/aybu/socialmedia/' . $translates["courses"] . "/" . $course->CourseID . '">' . $course->CourseCode . '</a>';
            }
        }
        echo json_encode($result);
        break;

    case 'enrollCourse':
        $CourseID = security("CourseID");
        $db->Insert("INSERT INTO membercourses SET MemberID = ?, CourseID = ?", array($memberid, $CourseID));
        $num = $db->getColumnData("SELECT COUNT(*) FROM membercourses WHERE CourseID = ?", array($CourseID));
        $result["attandance"] = $translates["peoplecourse"] . ": " . $num;
        echo json_encode($result);
        break;
    case 'quitCourse':
        $CourseID = security("CourseID");
        $db->Delete("DELETE FROM membercourses WHERE MemberID = ? AND CourseID = ?", array($memberid, $CourseID));
        $num = $db->getColumnData("SELECT COUNT(*) FROM membercourses WHERE CourseID = ?", array($CourseID));
        $result["attandance"] = $translates["peoplecourse"] . ": " . $num;
        echo json_encode($result);
        break;

    case 'searchCourse':
        $searchedKey = security("searchedKey");
        $allcourses = $db->getDatas("SELECT * FROM courses WHERE CourseName LIKE '$searchedKey%' ORDER BY CourseName ASC");
        if ($allcourses) {
            $result["courses"] = "";
            foreach ($allcourses as $course) {
                $ishaveCourse = $db->getData("SELECT * FROM membercourses WHERE MemberID = ? AND CourseID = ?", array($memberid, $course->CourseID));
                $result["courses"] .= '<div class="row my-2">
                                            <div class="col-1 text-end m-0 p-0"><input type="checkbox" id="selectCourse_' . $course->CourseID . '" courseid="' . $course->CourseID . '" class="form-check-input selectCourse" style="cursor: pointer;"';
                if ($ishaveCourse) {
                    $result["courses"] .= ' checked';
                }
                $result["courses"] .= '></div>
                                            <div class="col-11">
                                                <label for="selectCourse_' . $course->CourseID . '" class="w-100" style="cursor: pointer;">
                                                <div class="row">
                                                    <div class="col-8 ps-3 border-end">' . $course->CourseName . '</div>
                                                    <div class="col-4">' . $course->CourseCode . '</div>
                                                </div>
                                                </label>
                                            </div>
                                         </div>';
            }
        } else {
            $result["courses"] = "";
        }
        echo json_encode($result);
        break;

    case 'submitCourse':
        $CourseID = security("CourseID");
        $State = security("State");
        if ($State == "add") {
            $db->Insert("INSERT INTO membercourses SET MemberID = ?, CourseID = ?", array($memberid, $CourseID));
        } else {
            $db->Delete("DELETE FROM membercourses WHERE MemberID = ? AND CourseID = ?", array($memberid, $CourseID));
        }
        echo json_encode($result);
        break;
}
