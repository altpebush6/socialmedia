<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "functions/routing.php";
require_once "classes/AllClasses.php";
require_once "functions/security.php";

if(empty($_SERVER["HTTP_X_REQUESTED_WITH"]) or strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != 'xmlhttprequest'){
  header("Location: http://localhost/aybu/404.php");
}

$db = new aybu\db\mysqlDB(); 

$operation = $_GET['operation'];

switch($operation){
    case 'increaseLike':
        $id = $_POST['ID'];
        $postid = $_POST['PostID'];
        $insert_like = $db->Insert("INSERT INTO postlike SET PostID = ?, MemberID = ?",array($postid,$id));
        $count_like = $db->getColumnData("SELECT COUNT(*) FROM postlike WHERE PostID = ?",array($postid));
        echo "<a onClick=\"Like('decreaseLike','$postid')\">
                <i class='far fa-thumbs-down' style='cursor:pointer;color:#004485;'></i>
                <label style='cursor:pointer;color:#004485;'> Beğenme ($count_like)</label>
              </a>";
        break;
    case 'decreaseLike':
        $id = $_POST['ID'];
        $postid = $_POST['PostID'];
        $decrease_like = $db->Delete("DELETE FROM postlike WHERE PostID = ? AND MemberID = ?",array($postid,$id));
        $count_like = $db->getColumnData("SELECT COUNT(*) FROM postlike WHERE PostID = ?",array($postid));
        echo "<a onClick=\"Like('increaseLike','$postid')\">
                <i class='far fa-thumbs-up' style='cursor:pointer;'></i>
                <label style='cursor:pointer;'> Beğen ($count_like)</label>
              </a>";
        break;
    case 'sharecomment':
        $id = $_GET['ID'];
        $postid = $_GET['PostID'];

        $comment_text_name = 'text_'.$postid;
        $comment_text = $_POST[$comment_text_name];

        if(empty($comment_text)){
          $count_comment = $db->getColumnData("SELECT COUNT(*) FROM postcomments WHERE PostID = ?",array($postid));
          $message="<a onClick=\"openComments($postid)\">
          <i class='far fa-comment-alt' style='cursor:pointer;'></i>
          <label style='cursor:pointer;'> Yorum yap ($count_comment)</label>
        </a>:::";
        }
        else{
        $insert_comment = $db->Insert("INSERT INTO postcomments 
        SET PostID = ?, MemberID = ?, CommentText = ?",array($postid,$id,$comment_text));
        $count_comment = $db->getColumnData("SELECT COUNT(*) FROM postcomments WHERE PostID = ?",array($postid));
        $comment_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ? ",array($id));
        $comment_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?",array($id));
        $comment_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = ?",array($id));

        $last_comment = $db->getColumnData("SELECT CommentText FROM postcomments WHERE CommentID = ?",array($insert_comment));
        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($id));
        if (is_null($comment_profile_photo)) {
          if ($gender == 'Male') {
            $comment_profile_photo = "profilemale.png";
          } else {
            $comment_profile_photo = "profilefemale.png";
          }
        }
        $message="<a onClick=\"openComments($postid)\">
                    <i class='far fa-comment-alt' style='cursor:pointer;'></i>
                    <label style='cursor:pointer;'> Yorum yap ($count_comment)</label>
                  </a>:::
                  <div class=\"each-comment\">
                    <div class=\"each-comment-left\">
                      <img src='../profile/images_profile/$comment_profile_photo'>
                      <p><small>".$comment_name." ".$comment_lastname." - "."az önce</small>".$last_comment."</p>
                    </div>
                  </div>";
        }
      echo $message;
      break;
}

?>