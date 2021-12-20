<?php
function getmonth($month)
{
    require "classes/AllClasses.php";
    $SS = new aybu\session\session();
    if ($SS->isHave("Language")) {
        $language = $SS->get("Language");
    } else {
        $language = "tr";
    }
    // Dili çek
    require "languages/language_" . $language . ".php";
    switch ($month) {
        case 1:
            $month = $translates["jan"];
            break;
        case 2:
            $month = $translates["feb"];
            break;
        case 3:
            $month = $translates["march"];
            break;
        case 4:
            $month = $translates["april"];
            break;
        case 5:
            $month = $translates["may"];
            break;
        case 6:
            $month = $translates["june"];
            break;
        case 7:
            $month = $translates["july"];
            break;
        case 8:
            $month = $translates["august"];
            break;
        case 9:
            $month = $translates["sep"];
            break;
        case 10:
            $month = $translates["oct"];
            break;
        case 11:
            $month = $translates["nov"];
            break;
        case 12:
            $month = $translates["dec"];
            break;
    }
    return $month;
}
