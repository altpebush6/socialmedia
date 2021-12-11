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

$result = array();



switch ($operation) {
    case 'uploadprofileimg':
        $data = $_POST['image'];
        if (empty($data)) {
            $result["error"] = "<i class='fas fa-exclamation'></i> Lütfen Bir resim seçiniz.";
        } else {
            $old_profile_img_name = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = $memberid");
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
        $cover_img_name = $_FILES['image1']['name'];    //Kapak Fotoğrafına yüklenen resmin ismini al
        if (empty($cover_img_name)) {
            $result["error"] = "<i class='fas fa-exclamation'></i> Lütfen Bir resim seçiniz." . $cover_img_name . "";
        } else {
            $cover_image_extension = strtolower(pathinfo($cover_img_name, PATHINFO_EXTENSION));
            $allowed_file_extensions = array("png", "jpg", "jpeg", "jfif");
            if (!in_array($cover_image_extension, $allowed_file_extensions)) {
                $result["error"] = "<i class='fas fa-exclamation'></i> Sadece jpeg, jpg, jfif ve png uzantılı dosya yükleyebilirsiniz.";
            } else {

                $old_cover_img_name = $db->getColumnData("SELECT Member_Coverimg FROM images WHERE MemberID = $memberid");
                if (!is_null($old_cover_img_name)) {
                    $delete_img = "images_cover/" . $old_cover_img_name;
                    unlink($delete_img);          //  fotoğrafı klasörden sil
                }
                $cover_img_name = $memberNames . "_cover_" . uniqid() . "." . $cover_image_extension;
                $target = "images_cover/" . basename($cover_img_name);      // Hedefi images/resminismi yap  

                $sql = $db->Update("UPDATE
                                        images
                                        SET
                                        Member_Coverimg = ? WHERE MemberID = ?", array($cover_img_name, $memberid));   //Resmi Veritabanına Ekle  

                if (move_uploaded_file($_FILES['image1']['tmp_name'], $target)) {   //Dosyayı geçiçi yoldan hedef yola gönder
                    $result["success"] = "Resim başarıyla yüklendi.";
                } else {
                    $result["error"] = "<i class='fas fa-exclamation'></i> Resim yüklenemedi.";
                }
            }
        }

        echo json_encode($result);
        break;
}
