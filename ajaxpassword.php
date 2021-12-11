<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";
require_once "functions/time.php";

$db = new aybu\db\mysqlDB();
$SS = new aybu\session\session();

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}
require_once "languages/language_" . $language . ".php";

$AdminID = $SS->get("AdminID");

$result = array();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_pass = $db->getColumnData("SELECT AdminPassword FROM admins WHERE AdminID = ?", array($AdminID));
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
                                            $db->Update("UPDATE admins 
                                                SET AdminPassword = ? 
                                                WHERE AdminID = ?", array($pass_new, $AdminID));
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
