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
  case 'delMember':
    $MemberID = security("MemberID");
    $delete = $db->Update("UPDATE members SET MemberConfirm = ? WHERE MemberID = ?", array(2, $MemberID));
    $result["success"] = "ok";
    echo json_encode($result);
    break;
  case 'editMember':
    $MemberID = $_GET['MemberID'];
    $email = security("form_Email");
    $names = security("form_Names");
    $gender = security("form_Gender");
    $confirm = security("form_Confirm");

    $explodednames = explode(" ", $names);
    if ($explodednames[2]) {
      $name = $explodednames[0] . " " . $explodednames[1];
      $lastname = $explodednames[2];
    } else {
      $name = $explodednames[0];
      $lastname = $explodednames[1];
    }


    $updatedb = $db->Update("UPDATE members
                             SET MemberEmail = ?,
                              MemberName = ?,
                              MemberLastName = ?,
                              MemberNames = ?,
                              MemberGender = ?,
                              MemberConfirm = ? WHERE MemberID = ?", array($email, $name, $lastname, $names, $gender, $confirm, $MemberID));

    $result["email"] = $email;
    $result["names"] = $names;
    $result["gender"] = $gender;
    $result["confirm"] = $confirm;
    $result["test"] = $name;

    echo json_encode($result);
    break;

  case 'delAdmin':
    $AdminID = security("AdminID");
    $delete = $db->Update("UPDATE admins SET AdminConfirm = ? WHERE AdminID = ?", array(2, $AdminID));
    $result["success"] = "ok";
    echo json_encode($result);
    break;
  case 'editAdmin':
    $AdminID = $_GET['AdminID'];
    $email = security("form_Email");
    $names = security("form_Names");
    $gender = security("form_Gender");
    $confirm = security("form_Confirm");

    $explodednames = explode(" ", $names);
    if ($explodednames[2]) {
      $name = $explodednames[0] . " " . $explodednames[1];
      $lastname = $explodednames[2];
    } else {
      $name = $explodednames[0];
      $lastname = $explodednames[1];
    }


    $updatedb = $db->Update("UPDATE admins
                               SET AdminEmail = ?,
                                AdminName = ?,
                                AdminLastName = ?,
                                AdminNames = ?,
                                AdminGender = ?,
                                AdminConfirm = ? WHERE AdminID = ?", array($email, $name, $lastname, $names, $gender, $confirm, $AdminID));

    $result["email"] = $email;
    $result["names"] = $names;
    $result["gender"] = $gender;
    $result["confirm"] = $confirm;
    $result["test"] = $name;

    echo json_encode($result);
    break;
}
