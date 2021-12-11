<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "functions/routing.php";
require_once "classes/AllClasses.php";

if(empty($_SERVER["HTTP_X_REQUESTED_WITH"]) or strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != 'xmlhttprequest'){
    header("Location: http://localhost/aybu/socialmedia/404.php");
}

$db = new aybu\db\mysqlDB(); 
$SS = new aybu\session\session();

$memberid = $SS->get("MemberID");

if(isset($_POST['deleteimg1'])){      // Eğer kapak fotoğrafını sil'i seçerse

    $target=$db->getColumnData("SELECT
    Member_Coverimg
    FROM 
    images 
    WHERE MemberID = ? ",array($memberid)); //veritabanından kişiye ait kapak fotoğrafını ismini al

    $cover_img_name = $db->getColumnData("SELECT Member_Coverimg FROM images WHERE MemberID = $memberid");
    $delete_img="images_cover/".$cover_img_name;
    unlink($delete_img);          //  fotoğrafı klasörden sil

    $sql = $db->Update("UPDATE
    images
    SET
    Member_Coverimg = ? WHERE MemberID = ?",array(null,$memberid));    // Fotoğrafı veritabanından null değerine güncelle
    go("http://localhost/aybu/socialmedia/profil",0);
}

else if(isset($_POST['deleteimg2'])){      // Eğer profil fotoğrafını sil'i seçerse

    $target=$db->getColumnData("SELECT 
    Member_Profileimg 
    FROM images 
    WHERE MemberID = ? ",array($memberid)); //veritabanından kişiye ait profil fotoğrafını ismini al
    
    $profile_img_name = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = $memberid");
    $delete_img="images_profile/".$profile_img_name;
    unlink($delete_img);          //  fotoğrafı klasörden sil

    $sql = $db->Update("UPDATE
    images
    SET
    Member_Profileimg = ? WHERE MemberID = ?",array(null,$memberid));    // Fotoğrafı veritabanından null değerine güncelle
    go("http://localhost/aybu/socialmedia/profil",0);
}
else{
    header("Location: http://localhost/aybu/socialmedia/404.php");
}
?>