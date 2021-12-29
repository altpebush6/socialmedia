<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once "functions/routing.php";
require_once "classes/AllClasses.php";

if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) or strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != 'xmlhttprequest') {
    header("Location: http://localhost/aybu/socialmedia/404.php");
}
$db = new aybu\db\mysqlDB();
$SS = new aybu\session\session();

$memberid = $SS->get("MemberID");
$memberNames = $_GET['Names'];
$operation = $_GET['operation'];

$db = new aybu\db\mysqlDB();
$SS = new aybu\session\session();

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}
require_once "languages/language_" . $language . ".php";

$result = array();



switch ($operation) {
    case 'uploadprofileimg':
        $data = $_POST['image2'];
        if (empty($data)) {
            $result["error"] = '<i class="fas fa-exclamation"></i> ' . $translates["selectimg"];
        } else {
            $old_profile_img_name = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?",array($memberid));
            if (!is_null($old_profile_img_name)) {
                $delete_img = "images_profile/" . $old_profile_img_name;
                unlink($delete_img);          //  fotoğrafı klasörden sil
            }

            $image_array_1 = explode(";", $data);
            $image_array_2 = explode(",", $image_array_1[1]);
            $data = base64_decode($image_array_2[1]);

            $profile_img_name = $memberNames . time() . "_profile_" . uniqid() . ".png";

            $sql = $db->Update("UPDATE
                                        images
                                        SET
                                        Member_Profileimg = ? WHERE MemberID = ?", array($profile_img_name, $memberid));   //Resmi Veritabanına Ekle  

            $profile_img_name = "images_profile/" . $profile_img_name;

            file_put_contents($profile_img_name, $data);

            $result["success"] = "<img src='" . $profile_img_name . "'>";
        }

        echo json_encode($result);
        break;

    case 'uploadcoverimg':
        $data = $_POST['image1'];
        if (empty($data)) {
            $result["error"] = '<i class="fas fa-exclamation"></i> ' . $translates["selectimg"];
        } else {
            $old_cover_img_name = $db->getColumnData("SELECT Member_Coverimg FROM images WHERE MemberID = ?",array($memberid));
            if (!is_null($old_cover_img_name)) {
                $delete_img = "images_cover/" . $old_cover_img_name;
                unlink($delete_img);          //  fotoğrafı klasörden sil
            }

            $image_array_1 = explode(";", $data);
            $image_array_2 = explode(",", $image_array_1[1]);
            $data = base64_decode($image_array_2[1]);

            $cover_img_name = $memberNames . time() . "_cover_" . uniqid() . ".png";

            $sql = $db->Update("UPDATE
                                        images
                                        SET
                                        Member_Coverimg = ? WHERE MemberID = ?", array($cover_img_name, $memberid));   //Resmi Veritabanına Ekle  

            $cover_img_name = "images_cover/" . $cover_img_name;

            file_put_contents($cover_img_name, $data);

            $result["success"] = "<img src='" . $cover_img_name . "'>";
        }

        echo json_encode($result);
        break;
}
