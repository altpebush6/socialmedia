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

use aybu\token\token as Token;
use aybu\session\session as Session;

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
  case 'login':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $email = security("getemail");
      $password = security("getpassword");
      if (empty($email) || empty($password)) {
        $result["error"] = $translates["emptyemailorpass"];
      } else {
        $password = md5($password);

        $isadmin = $db->getDatas("SELECT * FROM admins WHERE AdminEmail = ? AND AdminPassword = ? AND AdminConfirm = ?", array($email, $password, 1));

        if ($isadmin) {
          $admin = $db->getData("SELECT * FROM admins WHERE AdminEmail = ?", array($email));
          $AdminID = $admin->AdminID;
          $result["adminlogedin"] = $AdminID;
          $SS = new aybu\session\session();
          session_regenerate_id(true);
          $SS->create("AdminLogedIn", true);
          $SS->create("AdminID", $AdminID);
          $result["success"] = $AdminID;
          $nowtime = date("d-m-Y H:i:s");
          $logintime = $db->Insert("INSERT INTO admintimes SET AdminID = ?, LoginTime = ?, LogoutTime = ?", array($AdminID, $nowtime, null));
          $SS->create("TimeID", $logintime);
        } else {
          $person = $db->getData("SELECT * FROM members WHERE MemberEmail = ?", array($email));

          $dbuser = $db->getDatas("SELECT * FROM members WHERE MemberEmail = ? AND MemberPass = ?", array($email, $password));

          $confirm = $db->getDatas("SELECT * FROM members WHERE MemberEmail = ? AND MemberPass = ? AND MemberConfirm = ?", array($email, $password, 1));

          $notactive = $db->getDatas("SELECT * FROM members WHERE MemberEmail = ? AND MemberPass = ? AND MemberConfirm = ?", array($email, $password, 2));

          if (!$dbuser || $notactive) {
            $result["error"] = $translates["wrongemailorpass"];
          } else {
            if (!$confirm) {
              $result["error"] = $translates["notconfirm"];
            } else {
              $SS = new aybu\session\session();
              session_regenerate_id(true);
              $SS->create("LogedIn", true);
              $MemberID = $person->MemberID;
              $SS->create("MemberID", $MemberID);
              $db->Update("UPDATE members SET MemberStatus = ? WHERE MemberID = ?", array(1, $MemberID));
              $result["success"] = $MemberID;
            } // if(!$confirm)
          } // if(!$dbuser || $notactive)
        } // if ($isadmin)
      } // if(empty($email) || empty($password))
    } // if($_SERVER['REQUEST_METHOD'] == 'POST')
    echo json_encode($result);
    break;

  case 'register':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $email = security("email");
      $password = security("password");
      $name = security("name");
      $lastname = security("lastname");
      $gender = security("gender");
      $contract = security("contract");
      $names = $name . " " . $lastname;

      $pattern_names = "/^[a-zA-ZıİğĞüÜçÇöÖşŞ\s]+$/u";
      $pattern_email = "/^[0-9]{11}$/";

      if (empty($email) or empty($password) or empty($name) or empty($lastname or empty($gender))) {
        $result["error"] = $translates["emptyareas"];
      } else {
        if (strlen($name) < 3 or strlen($name) > 30) {
          $result["error"] = $translates["namelength"];
        } else {
          if (strlen($lastname) < 2 or strlen($lastname) > 30) {
            $result["error"] = $translates["lastnamelength"];
          } else {
            if (!preg_match($pattern_names, $name) or !preg_match($pattern_names, $lastname)) {
              $result["error"] = $translates["invalidnames"];
            } else {
              if (!preg_match($pattern_email, $email)) {
                $result["error"] = $translates["invalidemail"];
              } else {
                $email = $email . "@ybu.edu.tr";
                $isHave = $db->getColumnData("SELECT * FROM members WHERE MemberEmail = ?", array($email));
                if ($isHave) {
                  $result["error"] = $translates["hasemail"];
                } else {
                  if (strlen($password) < 8 or strlen($password) > 20) {
                    $result["error"] = $translates["passlength"];
                  } else {
                    $password = md5($password);
                    if (!$contract) {
                      $result["error"] = $translates["confirmcontract"];
                    } else {
                      $result["MemberID"] = $insertdata;
                    } // if(empty($contract))
                  } // if(strlen($password)<8 or strlen($password)>20)
                } // if($isHave) Email kontrol
              } // if(!preg_match($pattern_email,$email)) 
            } // if(!preg_match($pattern_names,$name) or !preg_match($pattern_names,$lastname))
          } // if(strlen($lastname)<2 or strlen($lastname) >30)
        } // if(strlen($name)<3 or strlen($name) >30)
      } // if(empty($email) or empty($password) or empty($name) or empty($lastname) or empty($gender) or empty($birthday))

    } // if($_SERVER['REQUEST_METHOD'] == 'POST')
    echo json_encode($result);
    break;

  case 'departments':
    $chosenFaculty = security("chosenFaculty");
    $departments = $db->getDatas("SELECT * FROM departments_$language WHERE FacultyID = ?", array($chosenFaculty));
    foreach ($departments as $department) {
      $result["departments"] .= '<option value=" ' . $department->DepartmentID . ' ">' . $department->DepartmentName . '</option>';
    }
    echo json_encode($result);
    break;

  case 'completeReg':
    $MemberID = $_GET["MemberID"];
    $school = security("MemberSchool");
    $faculty = security("MemberFaculty");
    $department = security("MemberDepartment");

    $email = security("email");
    $email = $email . "@ybu.edu.tr";
    $password = security("password");
    $password = md5($password);
    $name = security("name");
    $lastname = security("lastname");
    $gender = security("gender");
    $contract = security("contract");
    $names = $name . " " . $lastname;

    $contact = security("contact");
    $pattern_contact = "/[^0-9 ]/";
    $pattern_contact2 = "/\s+/";
    $pattern_contact3 = "/^ ?0?\s?5[0-9]{2}\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}\s?$/";
    $replace = "";
    $contact = preg_replace($pattern_contact2, $replace, $contact);
    $contact = trim($contact);

    $birthday = security("birthday");

    if (empty($faculty) or empty($department) or empty($birthday)) {
      $result["error"] = $translates["emptyareas"];
    } else {
      if (preg_match($pattern_contact, $contact)) {    //$pattern_contact="/[^0-9 ]/";
        $result["error"] = $translates["onlynumber"];
        $result["errorinput"] = "#contact";
      } else {
        if (!(strlen($contact) == 10 || strlen($contact) == 11)) {
          $result["error"] = $translates["numberlength"];
          $result["errorinput"] = "#contact";
        } else {
          if (!preg_match($pattern_contact3, $contact)) { // $pattern_contact3="/^ ?0?\s?5[0-9]{2}\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}\s?$/";
            $result["error"] = $translates["invalidnumber"];
          } else {
            $ishave_contact = $db->getColumnData("SELECT * FROM memberabout WHERE MemberPhone = ? AND MemberID != ?", array($contact, $MemberID));
            if ($ishave_contact) {
              $result["error"] = $translates["hasnumber"];
            } else {
              $insertdata = $db->Insert("INSERT INTO members SET 
                MemberPass = ?,
                MemberEmail = ?,
                MemberName = ?,
                MemberLastName = ?,
                MemberNames = ?,
                MemberGender = ?,
                MemberConfirm = ?", array($password, $email, $name, $lastname, $names, $gender, 1));

              $insertinfo = $db->Insert("INSERT INTO memberabout SET MemberID = ?,
                MemberPhone = ?,
                MemberBirthday = ?,
                MemberUniversity = ?,
                MemberFaculty = ?,
                MemberDepartment = ?", array($insertdata, $contact, $birthday, $school, $faculty, $department));

              $insertdata2 = $db->Insert("INSERT INTO images SET
                MemberID = ?,
                Member_Profileimg = ?,
                Member_Coverimg = ?
                ", array($insertdata, null, null));

              $insertdata3 = $db->Insert("INSERT INTO memberresume SET
                MemberID = ? ", array($insertdata));
              $insertdata4 = $db->Insert("INSERT INTO memberbiography SET
                MemberID = ? ", array($insertdata));

              $result["success"] = $translates["registered"];
            }
          }
        }
      }
    }
    echo json_encode($result);
    break;
}
