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

$operation = $_GET['operation'];

$result = array();

switch ($operation) {
    case 'sendmail':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = security("email_forgot");
            if (empty($email)) {
                $result["error"] = $translates["emptyemail"];
                $result["errorinput1"] = "#email_forgot";
            } else {
                $pattern_email = "/^[0-9]{11}$/";
                if (!preg_match($pattern_email, $email)) {
                    $result["error"] = $translates["invalidemail"];
                    $result["errorinput1"] = "#email_forgot";
                } else {
                    $email = $email . "@ybu.edu.tr";
                    $isHave = $db->getColumnData("SELECT * FROM members WHERE MemberEmail = ?", array($email));
                    if (!$isHave) {
                        $result["error"] = $translates["notemail"];
                        $result["errorinput1"] = "#email_forgot";
                    } else {
                        $result["success"] = $translates["emaillink"];
                        $user_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberEmail = ?", array($email));
                        $user_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberEmail = ?", array($email));
                        $code = $user_name . "-" . $user_lastname . uniqid();
                        $SS = new aybu\session\session();
                        $SS->create("ResetPassCode", $code);
                        $SS->create("email", $email);
                        $result["code"] = $code;
                    }
                } //if(!preg_match($pattern_email,$email))
            } //if(empty($email))
        } //if($_SERVER['REQUEST_METHOD'] == 'POST')
        echo json_encode($result);
        break;
    case 'resetpass':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password1 = security("password_forgot1");
            $password2 = security("password_forgot2");
            if (empty($password1) and empty($password2)) {
                $result["error"] = $translates["emptypass"];
                $result["errorinput1"] = "#password_forgot1";
                $result["errorinput2"] = "#password_forgot2";
            } else {
                if (empty($password1)) {
                    $result["error"] = $translates["emptypass"];
                    $result["errorinput1"] = "#password_forgot1";
                } else {
                    if (empty($password2)) {
                        $result["error"] = $translates["emptypass"];
                        $result["errorinput1"] = "#password_forgot2";
                    } else {
                        if ($password1 != $password2) {
                            $result["error"] = $translates["passmatch"];
                            $result["errorinput1"] = "#password_forgot1";
                            $result["errorinput2"] = "#password_forgot2";
                        } else {
                            if (strlen($password1) < 8 or strlen($password1) > 20) {
                                $result["error"] = $translates["passlength"];
                                $result["errorinput1"] = "#password_forgot1";
                                $result["errorinput2"] = "#password_forgot2";
                            } else {
                                $SS = new aybu\session\session();
                                $code = $_POST["resetcode"];
                                if ($code != $SS->get("ResetPassCode")) {
                                    $result["error"] = $translates["errorresetpass"];
                                } else {
                                    $password = md5($password1);
                                    $db->Update("UPDATE members 
                                        SET MemberPass = ? 
                                        WHERE MemberEmail = ?", array($password, $SS->get("email")));
                                    $result["success"] = $translates["successresetpass"];
                                } // if($code != $SS->get("ResetPassCode"))
                            } // if(strlen($pass_new)<8 or strlen($pass_new)>20)
                        } // if($pass_new != $pass_new_again)
                    } // if(empty($pass_new)
                } // if(empty($pass_old))
            } // if(empty($password1) and empty($password2))
        } // if($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo json_encode($result);
        break;
}
