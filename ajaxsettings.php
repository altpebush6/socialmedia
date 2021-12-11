<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once "functions/routing.php";
require_once "classes/AllClasses.php";
require_once "functions/getmonth.php";
require_once "functions/security.php";
require_once "functions/time.php";

if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) or strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != 'xmlhttprequest') {
    header("Location: http://localhost/aybu/socialmedia/404.php");
}

$db = new aybu\db\mysqlDB();
$SS = new aybu\session\session();
$token = new aybu\token\token();

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
    case 'change_name_lastname':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name_lastname = security("name_lastname");
            $name_lastname2 = explode(" ", $name_lastname);
            $totalname = count($name_lastname2) - 1;
            $name = "";
            for ($i = 0; $i < $totalname; $i++) {
                if ($i == 0) {
                    $name .= $name_lastname2[0];
                } else {
                    $name .= " " . $name_lastname2[$i];
                }
            }
            $lastname = $name_lastname2[$totalname];
            $lastChangeTime = $db->getColumnData("SELECT NamesChangeTime FROM members WHERE MemberID = ?", array($memberid));
            if (is_null($lastChangeTime)) {
                $query = 1;
            } else {
                $query = 0;
            }
            $timeQuery = strtotime($lastChangeTime) - time();
            if ($timeQuery > 15552000 && $query == 0) {
                $result["error"] = $translates["timenames"];
                $result["errorinput"] = "#name_lastname";
            } else {
                $pattern_names = "/^[a-zA-ZıİğĞüÜçÇöÖşŞ\s]+$/u";
                if (empty($name) or empty($lastname)) {
                    $result["error"] = $translates["emptynames"];
                    $result["errorinput"] = "#name_lastname";
                } else {
                    if (strlen($name) < 3 or strlen($name) > 30) {
                        $result["error"] = $translates["namelength"];
                        $result["errorinput"] = "#name_lastname";
                    } else {
                        if (strlen($lastname) < 2 or strlen($lastname) > 30) {
                            $result["error"] = $translates["lastnamelength"];
                            $result["errorinput"] = "#name_lastname";
                        } else {
                            if (!preg_match($pattern_names, $name) or !preg_match($pattern_names, $lastname)) {
                                $result["error"] = $translates["invalidnames"];
                                $result["errorinput"] = "#name_lastname";
                            } else {
                                $afterSixMonths = time() + 15984000;
                                $afterSixMonths = date('Y-m-d H:i:s', $afterSixMonths);
                                $result["success"] = "Ad-Soyad başarıyla değiştirildi.";
                                $db->Update("UPDATE members SET
                                MemberName= ?, MemberLastName= ?, MemberNames = ?, NamesChangeTime=?
                                WHERE MemberID = ?", array($name, $lastname, $name_lastname, $afterSixMonths, $memberid));
                            } // if(!preg_match($pattern_names,$name) or !preg_match($pattern_names,$lastname))
                        } // if(strlen($lastname)<2 or strlen($lastname) >30)
                    } // if(strlen($name)<3 or strlen($name) >30)
                } // if(empty($name) or empty($lastname))
            } // if ($timeQuery < 15552000  && $query = 0) {
        } // if($_SERVER['REQUEST_METHOD'] == 'POST')
        echo json_encode($result);
        break;

    case 'change_phonenum':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phonenum = security("phonenum");
            $pattern_phonenum = "/[^0-9 ]/";
            $pattern_phonenum2 = "/\s+/";
            $pattern_phonenum3 = "/^ ?0?\s?5[0-9]{2}\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}\s?$/";
            if (preg_match($pattern_phonenum, $phonenum)) {    //$pattern_phonenum="/[^0-9 ]/";
                $result["error"] = $translates["onlynumber"];
                $result["errorinput"] = "#phonenum";
            } else {
                $replace = "";
                $phonenum = preg_replace($pattern_phonenum2, $replace, $phonenum);
                $phonenum = trim($phonenum);
                if (!(strlen($phonenum) == 10 || strlen($phonenum) == 11)) {
                    $result["error"] = $translates["numberlength"];
                    $result["errorinput"] = "#phonenum";
                } else {
                    if (!preg_match($pattern_phonenum3, $phonenum)) { // $pattern_phonenum3="/^ ?0?\s?5[0-9]{2}\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}\s?$/";
                        $result["error"] = $translates["invalidnumber"];
                        $result["errorinput"] = "#phonenum";
                    } else {
                        $ishave_phonenum = $db->getColumnData("SELECT * FROM memberabout WHERE MemberPhone = ? AND MemberID != ?", array($phonenum, $memberid));
                        if ($ishave_phonenum) {
                            $result["error"] = $translates["hasnumber"];
                            $result["errorinput"] = "#phonenum";
                        } else {
                            $db->Update("UPDATE memberabout SET
                                MemberPhone= ?
                                WHERE MemberID = ?", array($phonenum, $memberid));
                            $result["success"] = "ok";
                        } // if($ishave_phonenum)
                    } // if(!preg_match($pattern_phonenum3,$phonenum))
                } // if(!(strlen($phonenum)==10 || strlen($phonenum)==11))
            } // if(preg_match($pattern_phonenum,$phonenum))
        }
        echo json_encode($result);
        break;

        //ABOUT SETTINGS

    case 'change_birthday':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $birthday = security("birthday");
            $timeQuery = time() - strtotime($birthday);
            $today = date("Y-m-d");
            $diff = date_diff(date_create($birthday), date_create($today));
            if ($timeQuery < 0) {
                $result["error"] = $translates["invaliddate"];
            } else {
                if ($diff->format('%y') < 17) {
                    $result["error"] = $translates["lessvntn"];
                } else {
                    if (empty($birthday)) {
                        $db->Update("UPDATE memberabout SET
                            MemberBirthday = ?
                            WHERE MemberID = ?", array(null, $memberid));
                        $result["success"] = $translates["undefined"];
                    } else {
                        $db->Update("UPDATE memberabout SET
                            MemberBirthday= ?
                            WHERE MemberID = ?", array($birthday, $memberid));
                        $birthday = explode("-", $birthday);
                        $result["success"] = $birthday[2] . " " . getmonth($birthday[1]) . " " . $birthday[0];
                    }
                }
            }
        }
        echo json_encode($result);
        break;

    case 'change_faculty':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $faculty = security("faculty");
            if (empty($faculty)) {
                $db->Update("UPDATE memberabout SET
                        MemberFacultyID= ?
                        WHERE MemberID = ?", array(null, $memberid));
                $db->Update("UPDATE memberabout SET
                        MemberDepartmentID= ?
                        WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = "ok";
            } else {
                $db->Update("UPDATE memberabout SET
                        MemberFacultyID= ?
                        WHERE MemberID = ?", array($faculty, $memberid));
                $result["success"] = "ok";
            }
        }
        echo json_encode($result);
        break;

    case 'change_department': //FAKÜLTENİN VARLIĞINI SORGULA
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $department = security("department");
            if (empty($department)) {
                $db->Update("UPDATE memberabout SET
                        MemberDepartmentID= ?
                        WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = "Bölümünüz başarıyla değiştirildi.:::succe";
            } else {
                $db->Update("UPDATE memberabout SET
                        MemberDepartmentID= ?
                        WHERE MemberID = ?", array($department, $memberid));
                $result["success"] = "Bölümünüz başarıyla değiştirildi.";
            }
        }
        echo json_encode($result);
        break;

    case 'openEditDepartment':
        $facultyID = security("FacultyID");
        if ($facultyID > 0) {
            $result["output"] = '<option value="0" disabled selected>' . $translates["choosedepartment"] . '</option>';
            $departments = $db->getDatas("SELECT * FROM departments_$language WHERE FacultyID = ?", array($facultyID));
            foreach ($departments as $department) {
                $result["output"] .= '<option value="' . $department->DepartmentID . '">' . $department->DepartmentName . '</option>';
            }
        }
        echo json_encode($result);
        break;

    case 'change_hobbies':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $hobbies = security("hobbies");
            if (empty($hobbies)) {
                $db->Update("UPDATE memberabout SET
                    MemberHobbies= ?
                    WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberabout SET
                    MemberHobbies= ?
                    WHERE MemberID = ?", array($hobbies, $memberid));
                $result["success"] = $hobbies;
            }
        }
        echo json_encode($result);
        break;


    case 'change_tv':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tvseries = security("tv");
            if (empty($tvseries)) {
                $db->Update("UPDATE memberabout SET
                    MemberFavTV= ?
                    WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberabout SET
                    MemberFavTV= ?
                    WHERE MemberID = ?", array($tvseries, $memberid));
                $result["success"] = $tvseries;
            }
        }
        echo json_encode($result);
        break;

    case 'change_hometown':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $hometown = security("hometown");
            $hometown = $db->getColumnData("SELECT CityName FROM cities WHERE CityID = ?", array($hometown));
            if (empty($hometown)) {
                $db->Update("UPDATE memberabout SET
                    MemberHometown= ?
                    WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberabout SET
                    MemberHometown= ?
                    WHERE MemberID = ?", array($hometown, $memberid));
                $result["success"] = $hometown;
            }
        }
        echo json_encode($result);
        break;

    case 'change_city':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $city = security("city");
            $city = $db->getColumnData("SELECT CityName FROM cities WHERE CityID = ?", array($city));
            if (empty($city)) {
                $db->Update("UPDATE memberabout SET
                    MemberCity= ?
                    WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberabout SET
                    MemberCity= ?
                    WHERE MemberID = ?", array($city, $memberid));
                $result["success"] = $city;
            }
        }
        echo json_encode($result);
        break;

    case 'change_pass':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db_pass = $db->getColumnData("SELECT MemberPass FROM members WHERE MemberID = ?", array($memberid));
            $pass_old = security("pass_old");
            $pass_new = security("pass_new");
            $pass_new_again = security("pass_new_again");

            if (empty($pass_old) and empty($pass_new) and empty($pass_new_again)) {
                $result["error"] = $translates["emptypass"];
                $result["errorinput1"] = "#pass_old";
                $result["errorinput2"] = "#pass_new";
                $result["errorinput3"] = "#pass_new_again";
            } else {
                if (empty($pass_old) and empty($pass_new)) {
                    $result["error"] = $translates["emptyoldnewpass"];
                    $result["errorinput1"] = "#pass_old";
                    $result["errorinput2"] = "#pass_new";
                } else {
                    if (empty($pass_new) and empty($pass_new_again)) {
                        $result["error"] = $translates["emptynewpass"];
                        $result["errorinput1"] = "#pass_new";
                        $result["errorinput2"] = "#pass_new_again";
                    } else {
                        if (empty($pass_old) and empty($pass_new_again)) {
                            $result["error"] = $translates["emptyoldnewpass"];
                            $result["errorinput1"] = "#pass_old";
                            $result["errorinput2"] = "#pass_new_again";
                        } else {
                            if (empty($pass_old)) {
                                $result["error"] = $translates["emptyoldpass"];
                                $result["errorinput1"] = "#pass_old";
                            } else {
                                if (empty($pass_new)) {
                                    $result["error"] = $translates["emptynewpass"];
                                    $result["errorinput1"] = "#pass_new";
                                } else {
                                    if (empty($pass_new_again)) {
                                        $result["error"] = $translates["emptyagainpass"];
                                        $result["errorinput1"] = "#pass_new_again";
                                    } else {
                                        $pass_old = md5($pass_old);
                                        if ($pass_old != $db_pass) {
                                            $result["error"] = $translates["wrongpass"];
                                            $result["errorinput1"] = "#pass_old";
                                        } else {
                                            if ($pass_new != $pass_new_again) {
                                                $result["error"] = $translates["passmatch"];
                                                $result["errorinput1"] = "#pass_new";
                                                $result["errorinput2"] = "#pass_new_again";
                                            } else {
                                                if (strlen($pass_new) < 8 or strlen($pass_new) > 20) {
                                                    $result["error"] = $translates["passlength"];
                                                    $result["errorinput1"] = "#pass_new";
                                                    $result["errorinput2"] = "#pass_new_again";
                                                } else {
                                                    $pass_new = md5($pass_new);
                                                    $db->Update("UPDATE members 
                                                        SET MemberPass = ? 
                                                        WHERE MemberID = ?", array($pass_new, $memberid));
                                                    $result["success"] = "Şifeniz başarıyla değiştirildi.";
                                                } // if(strlen($pass_new)<8 or strlen($pass_new)>20)
                                            } // if($pass_new != $pass_new_again)
                                        } // if($pass_old != $db_pass)
                                    } // if(empty($pass_new_again))
                                } // if(empty($pass_new)
                            } // if(empty($pass_old))
                        } // if(empty($pass_old) and empty($pass_new_again))
                    } // if(empty($pass_new) and empty($pass_new_again))
                } // if(empty($pass_old) and empty($pass_new))
            } // if(empty($pass_old) and empty($pass_new) and empty($pass_new_again))
        } // if($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo json_encode($result);
        break;

    case 'deletemember':
        $delete = $db->Update("UPDATE members SET MemberConfirm = ? WHERE MemberID = ?", array(2, $memberid));
        break;

    case 'change_m_school':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $school = security("m_school");
            if (empty($school)) {
                $db->Update("UPDATE memberresume SET
                    MiddleSchool= ?
                    WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberresume SET
                    MiddleSchool= ?
                    WHERE MemberID = ?", array($school, $memberid));
                $result["success"] = $school;
            }
        }
        echo json_encode($result);
        break;
    case 'change_h_school':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $school = security("h_school");
            if (empty($school)) {
                $db->Update("UPDATE memberresume SET
                        HighSchool= ?
                        WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberresume SET
                        HighSchool= ?
                        WHERE MemberID = ?", array($school, $memberid));
                $result["success"] = $school;
            }
        }
        echo json_encode($result);
        break;
    case 'change_a_degree':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $school = security("a_degree");
            if (empty($school)) {
                $db->Update("UPDATE memberresume SET
                            AssociateDegree= ?
                            WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberresume SET
                            AssociateDegree= ?
                            WHERE MemberID = ?", array($school, $memberid));
                $result["success"] = $school;
            }
        }
        echo json_encode($result);
        break;
    case 'change_degree':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $school = security("degree");
            if (empty($school)) {
                $db->Update("UPDATE memberresume SET
                                Degree= ?
                                WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberresume SET
                                Degree= ?
                                WHERE MemberID = ?", array($school, $memberid));
                $result["success"] = $school;
            }
        }
        echo json_encode($result);
        break;
    case 'change_m_degree':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $school = security("m_degree");
            if (empty($school)) {
                $db->Update("UPDATE memberresume SET
                                    MasterDegree= ?
                                    WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberresume SET
                                    MasterDegree= ?
                                    WHERE MemberID = ?", array($school, $memberid));
                $result["success"] = $school;
            }
        }
        echo json_encode($result);
        break;
    case 'change_d_degree':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $school = security("d_degree");
            if (empty($school)) {
                $db->Update("UPDATE memberresume SET
                                        DoctorDegree= ?
                                        WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberresume SET
                                        DoctorDegree= ?
                                        WHERE MemberID = ?", array($school, $memberid));
                $result["success"] = $school;
            }
        }
        echo json_encode($result);
        break;
    case 'change_j_exp':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $job = security("jobs");
            if (empty($job)) {
                $db->Update("UPDATE memberresume SET
                                            JobExperiments= ?
                                            WHERE MemberID = ?", array(null, $memberid));
                $result["success"] = $translates["undefined"];
            } else {
                $db->Update("UPDATE memberresume SET
                                            JobExperiments= ?
                                            WHERE MemberID = ?", array($job, $memberid));
                $result["success"] = $job;
            }
        }
        echo json_encode($result);
        break;

    case 'removeJob':
        $jobs = security("Jobs");
        $removeJob =  security("RemoveJob");
        $removeJob =  security("RemoveJob") . ",";
        $pattern1 = "/\(/";
        $removeJob = preg_replace($pattern1, "\(", $removeJob);
        $pattern2 = "/\)/";
        $removeJob = preg_replace($pattern2, "\)", $removeJob);
        $result["pattern"] = $removeJob;
        $pattern3 = "/" . $removeJob . "/";
        $jobs = preg_replace($pattern3, " ", $jobs);
        $result["success"] = $jobs;
        echo json_encode($result);
        break;
}
