<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "functions/security.php";
require_once "classes/AllClasses.php";

$SS = new aybu\session\session();

$result = array();

$toLang = security("toLang");
$currentpage = security("currentpage");
$currentpart = security("currentpart");

$SS->create("Language",$toLang);

if($SS->isHave("Language")){
  $language = $SS->get("Language");
}
else{
  $language = "tr";
}
require_once "languages/language_".$language.".php";

$result["currentpage"] = $translates[$currentpage];
$result["currentpart"] = $translates[$currentpart];

echo json_encode($result);
?>