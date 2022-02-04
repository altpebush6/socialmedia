<?php
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('Europe/Istanbul');

require_once "functions/time.php";
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
$postid = $_GET['PostID'];
$memberid = $SS->get("MemberID");
$user_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = $memberid ");
$user_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = $memberid ");

$result = array();

switch ($operation) {
  case 'posting':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $text = security('text_post');
      if (empty($text)) { // Text yoksa
        $text = null;
      }

      // IMG yoksa
      $imagesnames = array();
      $imagecounter = 0;
      if (empty($_FILES['image']['name'][0])) {
        $imagesnames[0] = null;
      } else { // IMG varsa
        while ($_FILES['image']['name'][$imagecounter]) {
          $img_name_ext = strtolower(pathinfo($_FILES['image']['name'][$imagecounter], PATHINFO_EXTENSION));
          $imagesnames[$imagecounter] = $user_name . "_" . $user_lastname . "_" . uniqid() . rand() . "." . $img_name_ext;
          $target = "post_images/" . basename($imagesnames[$imagecounter]);
          $image_tmp_name = $_FILES['image']["tmp_name"][$imagecounter];
          move_uploaded_file($image_tmp_name, $target);
          $imagecounter += 1;
        }
      }

      //Dosya yoksa
      $filecounter = 0;
      $filesnames = array();
      if (empty($_FILES['files']['name'][0])) {
        $filesnames[0] = null;
      } else { // Dosya varsa
        while ($_FILES['files']['name'][$filecounter]) {
          $filesnames[$filecounter] = $_FILES['files']['name'][$filecounter];
          $target = "post_files/" . basename($filesnames[$filecounter]);
          $file_tmp_name = $_FILES['files']["tmp_name"][$filecounter];
          move_uploaded_file($file_tmp_name, $target);
          $filecounter += 1;
        }
      }

      // Konu varsa
      $topic = security("post_part");
      if ($topic) {
        $gettopicCount = $db->getColumnData("SELECT TopicInteraction FROM topics WHERE TopicLink = ?", array($topic));
        $newCount = $gettopicCount + 1;
        $addtopicCount = $db->Update("UPDATE topics SET TopicInteraction = ? WHERE TopicLink = ?", array($newCount, $topic));
      } else { // Konu yoksa
        $topic = null;
      }

      // Kulüp varsa
      $clubid = security("ClubID");
      if ($clubid == 0) {
        $clubid = null;
      }

      //Hepsi Boşsa
      if (!$text && empty($_FILES['image']['name'][0]) && empty($_FILES['files']['name'][0])) {
        $result["empty"] = true;
      } else {
        $sql = $db->Insert("INSERT posts SET MemberID = ?, PostText = ?, PostImg = ?, PostFile = ?, PostClub = ?, PostTopic = ?", array($memberid, $text, $imagesnames[0], $filesnames[0], $clubid, $topic));   //Resmi Veritabanına Ekle        
        if ($imagecounter > 1) {
          for ($i = 1; $i < $imagecounter; $i++) {
            $j = $i + 1;
            $addimages = $db->Update("UPDATE posts SET PostImg$j = ? WHERE PostID = ?", array($imagesnames[$i], $sql));
          }
        }
        if ($filecounter > 1) {
          for ($k = 1; $k < $filecounter; $k++) {
            $l = $k + 1;
            $addfiles = $db->Update("UPDATE posts SET PostFile$l = ? WHERE PostID = ?", array($filesnames[$k], $sql));
          }
        }
      }
    }
    $result["success"] = "ok";
    echo json_encode($result);
    break;
  case 'editpost':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $PostID = $_GET["PostID"];
      $text = security('edittedtext_post');
      if (empty($text)) { // Text yoksa
        $text = null;
        $result["newText"] = "";
      } else {
        $result["newText"] = $text;
      }

      // IMG yoksa
      $imagesnames = array();
      $imagecounter = 0;
      if (empty($_FILES['edit_image_upload']['name'][0])) {
        $imagechange = "dontchange";
      } else { // IMG varsa
        $imagechange = "change";
        while ($_FILES['edit_image_upload']['name'][$imagecounter]) {
          $img_name_ext = strtolower(pathinfo($_FILES['edit_image_upload']['name'][$imagecounter], PATHINFO_EXTENSION));
          $imagesnames[$imagecounter] = $user_name . "_" . $user_lastname . "_" . uniqid() . rand() . "." . $img_name_ext;
          $target = "post_images/" . basename($imagesnames[$imagecounter]);
          $image_tmp_name = $_FILES['edit_image_upload']["tmp_name"][$imagecounter];
          move_uploaded_file($image_tmp_name, $target);
          $result["newImages"] .= '<a href="post_images/' . $imagesnames[$imagecounter] . '" class="col-6 mx-auto d-flex justify-content-center align-items-center">
                                    <img src="post_images/' .  $imagesnames[$imagecounter] . '" style="max-width:100%;min-width:100px;max-height:64vh;border-radius:5px;">
                                  </a>
                                  <script>
                                  baguetteBox.run(".postmiddle_ ' . $PostID . '");
                                  </script>';
          $imagecounter += 1;
        }
      }

      //Dosya yoksa
      $filecounter = 0;
      $filesnames = array();
      if (empty($_FILES['edit_file_upload']['name'][0])) {
        $fileschange = "dontchange";
      } else { // Dosya varsa
        $fileschange = "change";
        while ($_FILES['edit_file_upload']['name'][$filecounter]) {
          $filesnames[$filecounter] = $_FILES['edit_file_upload']['name'][$filecounter];
          $target = "post_files/" . basename($filesnames[$filecounter]);
          $file_tmp_name = $_FILES['edit_file_upload']["tmp_name"][$filecounter];
          move_uploaded_file($file_tmp_name, $target);
          $result["newFiles"] .= '<div class="col-12 my-2 ps-4 fs-6">
          <a class="text-dark" href="http://localhost/aybu/socialmedia/' . $translates["home"] . '?download=' . $filesnames[$filecounter] . '"><i class="text-dark fas fa-file-alt fa-2x"></i> ' . $filesnames[$filecounter] . '</a>
          </div>';
          $filecounter += 1;
        }
      }
      if ($fileschange == "dontchange" && $imagechange == "dontchange") {
        $updatepost = $db->Update("UPDATE posts SET PostText = ? WHERE PostID = ?", array($text, $PostID));
      } elseif ($fileschange == "change" && $imagechange == "dontchange") {
        $deletefiles = $db->Update("UPDATE posts SET PostFile = ?, PostFile2 = ?, PostFile3 = ?, PostFile4 = ? WHERE PostID = ?", array(null, null, null, null, $PostID));
        $updatepost = $db->Update("UPDATE posts SET PostText = ?, PostFile = ? WHERE PostID = ?", array($text, $filesnames[0], $PostID));
        if ($filecounter > 1) {
          for ($k = 1; $k < $filecounter; $k++) {
            $l = $k + 1;
            $updatefiles = $db->Update("UPDATE posts SET PostFile$l = ? WHERE PostID = ?", array($filesnames[$k], $PostID));
          }
        }
      } elseif ($fileschange == "dontchange" && $imagechange == "change") {
        $deleteimages = $db->Update("UPDATE posts SET PostImg = ?, PostImg2 = ?, PostImg3 = ?, PostImg4 = ? WHERE PostID = ?", array(null, null, null, null, $PostID));
        $updatepost = $db->Update("UPDATE posts SET PostText = ?, PostImg = ? WHERE PostID = ?", array($text, $imagesnames[0], $PostID));
        if ($imagecounter > 1) {
          for ($i = 1; $i < $imagecounter; $i++) {
            $j = $i + 1;
            $addimages = $db->Update("UPDATE posts SET PostImg$j = ? WHERE PostID = ?", array($imagesnames[$i], $PostID));
          }
          $result["state"] = $j;
        }
      } else {
        $deletefiles = $db->Update("UPDATE posts SET PostFile = ?, PostFile2 = ?, PostFile3 = ?, PostFile4 = ? WHERE PostID = ?", array(null, null, null, null, $PostID));
        $deleteimages = $db->Update("UPDATE posts SET PostImg = ?, PostImg2 = ?, PostImg3 = ?, PostImg4 = ? WHERE PostID = ?", array(null, null, null, null, $PostID));
        $updatepost = $db->Update("UPDATE posts SET PostText = ?, PostImg = ?, PostFile = ? WHERE PostID = ?", array($text, $imagesnames[0], $filesnames[0], $PostID));
        if ($imagecounter > 1) {
          for ($i = 1; $i < $imagecounter; $i++) {
            $j = $i + 1;
            $addimages = $db->Update("UPDATE posts SET PostImg$j = ? WHERE PostID = ?", array($imagesnames[$i], $PostID));
          }
        }
        if ($filecounter > 1) {
          for ($k = 1; $k < $filecounter; $k++) {
            $l = $k + 1;
            $updatefiles = $db->Update("UPDATE posts SET PostFile$l = ? WHERE PostID = ?", array($filesnames[$k], $PostID));
          }
        }
      }
      $result["success"] = "ok";
    }
    echo json_encode($result);
    break;

  case 'repPost':
    $diduRep = $db->getData("SELECT * FROM reports_posts WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $postid));
    if (!$diduRep) {
      $getPostReport = $db->getColumnData("SELECT PostReport FROM posts WHERE PostID = ?", array($postid));
      $newReport = $getPostReport + 1;
      $setPostReport = $db->Update("UPDATE posts SET PostReport = ? WHERE PostID = ?", array($newReport, $postid));
      $setPostReport2 = $db->Update("INSERT reports_posts SET ReporterID = ?, ReportedID = ?", array($memberid, $postid));
      echo $translates["reported"];
    }
    break;

  case 'delrepPost':
    $diduRep = $db->getData("SELECT * FROM reports_posts WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $postid));
    if ($diduRep) {
      $getPostReport = $db->getColumnData("SELECT PostReport FROM posts WHERE PostID = ?", array($postid));
      $newReport = $getPostReport - 1;
      $setPostReport = $db->Update("UPDATE posts SET PostReport = ? WHERE PostID = ?", array($newReport, $postid));
      $setPostReport2 = $db->Delete("DELETE FROM reports_posts WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $postid));
    }
    break;

  case 'repComment':
    $commentid = $_GET["CommentID"];
    $postid = $db->getColumnData("SELECT PostID FROM postcomments WHERE CommentID = ?", array($commentid));
    $diduRep = $db->getData("SELECT * FROM reports_comments WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $commentid));
    if (!$diduRep) {
      $getCommentReport = $db->getColumnData("SELECT CommentReport FROM postcomments WHERE CommentID = ?", array($commentid));
      $newReport = $getCommentReport + 1;
      $setCommentReport = $db->Update("UPDATE postcomments SET CommentReport = ? WHERE CommentID = ?", array($newReport, $commentid));
      $setCommentReport2 = $db->Update("UPDATE posts SET CommentReport = ? WHERE PostID = ?", array($newReport, $postid));
      $setCommentReport3 = $db->Update("INSERT reports_comments SET ReporterID = ?, ReportedID = ?, ReportedPostID = ?", array($memberid, $commentid, $postid));
      echo $translates["reported"];
    }
    break;

  case 'delrepComment':
    $commentid = $_GET["CommentID"];
    $postid = $db->getColumnData("SELECT PostID FROM postcomments WHERE CommentID = ?", array($commentid));
    $diduRep = $db->getData("SELECT * FROM reports_comments WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $commentid));
    if ($diduRep) {
      $getCommentReport = $db->getColumnData("SELECT CommentReport FROM postcomments WHERE CommentID = ?", array($commentid));
      $newReport = $getCommentReport - 1;
      $setCommentReport = $db->Update("UPDATE postcomments SET CommentReport = ? WHERE CommentID = ?", array($newReport, $commentid));
      $setCommentReport2 = $db->Update("UPDATE posts SET CommentReport = ? WHERE PostID = ?", array($newReport, $postid));
      $setCommentReport3 = $db->Delete("DELETE FROM reports_comments WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $commentid));
    }
    break;

  case 'deletepost':
    $deletepost = $db->Update("UPDATE posts SET PostActive = ? WHERE PostID = ?", array(0, $postid));
    $deletecomments = $db->Update("UPDATE postcomments SET CommentActive = ? WHERE PostID = ?", array(0, $postid));
    $deletelikes = $db->Delete("DELETE FROM postlike WHERE PostID = ?", array($postid));
    $topic = $db->getColumnData("SELECT PostTopic FROM posts WHERE PostID = ?", array($postid));
    $gettopicCount = $db->getColumnData("SELECT TopicInteraction FROM topics WHERE TopicLink = ?", array($topic));
    $newCount = $gettopicCount - 1;
    $addtopicCount = $db->Update("UPDATE topics SET TopicInteraction = ? WHERE TopicLink = ?", array($newCount, $topic));
    break;

  case 'increaseLike':
    $id = $_POST['ID'];
    $postid = $_POST['PostID'];
    $insert_like = $db->Insert("INSERT INTO postlike SET PostID = ?, MemberID = ?", array($postid, $id));
    $count_like = $db->getColumnData("SELECT COUNT(*) FROM postlike WHERE PostID = ?", array($postid));
    $result["like"] = "<a class='text-decoration-none' onClick=\"Like('decreaseLike','" . $postid . "')\" style='color:#5a49e3'>
                          <i class='far fa-thumbs-down' style='cursor:pointer;'></i>
                          <label style='cursor:pointer;'> " . $translates['dislikepost'] . " (" . $count_like . ")</label>
                        </a>";
    echo json_encode($result);
    break;

  case 'decreaseLike':
    $id = $_POST['ID'];
    $postid = $_POST['PostID'];
    $decrease_like = $db->Delete("DELETE FROM postlike WHERE PostID = ? AND MemberID = ?", array($postid, $id));
    $count_like = $db->getColumnData("SELECT COUNT(*) FROM postlike WHERE PostID = ?", array($postid));
    $result["like"] = "<a class='text-decoration-none text-dark' onClick=\"Like('increaseLike','" . $postid . "')\">
                          <i class='far fa-thumbs-up' style='cursor:pointer;'></i>
                          <label style='cursor:pointer;'> " . $translates['likepost'] . " (" . $count_like . ")</label>
                        </a>";
    echo json_encode($result);
    break;

  case 'sharecomment':
    $id = $_GET['ID'];
    $postid = $_GET['PostID'];

    $comment_text_name = 'text_' . $postid;
    $comment_text = $_POST[$comment_text_name];

    if (empty($comment_text)) {
      $result["success"] = "no";
    } else {
      $insert_comment = $db->Insert("INSERT INTO postcomments 
          SET PostID = ?, MemberID = ?, CommentText = ?", array($postid, $id, $comment_text));
      $count_comment = $db->getColumnData("SELECT COUNT(*) FROM postcomments WHERE PostID = ? AND CommentActive = ?", array($postid, 1));
      $comment_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ? ", array($id));
      $comment_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($id));
      $comment_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = ?", array($id));

      $last_comment = $db->getColumnData("SELECT CommentText FROM postcomments WHERE CommentID = ?", array($insert_comment));

      $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($id));
      if (is_null($comment_profile_photo)) {
        if ($gender == 'Male') {
          $comment_profile_photo = "profilemale.png";
        } else {
          $comment_profile_photo = "profilefemale.png";
        }
      }

      $result["commentcounter"] = $count_comment;


      $comment_time = $db->getColumnData("SELECT CommentAddTime FROM postcomments WHERE CommentID = ?", array($insert_comment));
      $diff_comment = calculateTime($comment_time);

      $result["comment"] = '<div class="row align-items-center text-dark px-3 py-1" id="each_comment_' . $insert_comment . '">
                                <div class="col-1 p-0 pt-2 d-flex align-items-start justify-content-start">     
                                  <a href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $id . '">
                                    <img src="images_profile/' . $comment_profile_photo . '" class="rounded-circle" width="40" height="40">       
                                  </a>            
                                </div>  
                                <div class="col-9 ps-3 p-md-0">
                                  <div class="col-12">
                                    <a class="text-dark text-decoration-none text-start" href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $id . '">
                                      <small>' . $comment_name . ' ' . $comment_lastname . ' - ' . $diff_comment . '</small>
                                    </a>
                                  </div>
                                  <div class="col-12 text-break">
                                    <p class="m-0" id="comment_text_' . $insert_comment . '">' . $comment_text . '</p>
                                    <form class="d-none" method="post" id="form_editcomment_' . $insert_comment . '">
                                      <div class="row align-items-center">
                                        <div class="col-10 text-center">
                                          <input autocomplete="off" maxlength="100" class="create-comment form-control form-control-sm rounded-3" type="text" maxlength="100" value="' . $comment_text . '" name="edittedcomment_' . $insert_comment . '" id="edittedcomment_' . $insert_comment . '" placeholder="' . $translates["saysth"] . '">
                                        </div>
                                        <div class="col-1 p-0 m-0">
                                          <button type="button" class="btn btn-outline-dark btn-sm rounded-3" onClick=\'CommentOperate("editComment","' . $postid . '","' . $insert_comment . '")\'>
                                            <i class="far fa-paper-plane"></i>
                                          </button>
                                        </div>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                                <div class="col-2 text-end p-0">
                                  <small>
                                    <i class="icon-edit fas fa-pencil-alt me-2" style="cursor:pointer;" id="editComment" onClick=\'OpenEditComment("' . $insert_comment . '","' . $postid . '")\'></i>
                                    <i class="icon-delete fas fa-trash-alt" style="cursor:pointer;" id="deleteComment" onClick=\'CommentOperate("deleteComment","' . $postid . '","' . $insert_comment . '")\'></i>
                                  </small>
                                </div>
                              </div>';

      $result["success"] = "ok";
    }

    echo json_encode($result);
    break;

  case 'editComment':
    $id = $_GET['ID'];
    $CommentID = $_GET['CommentID'];
    $commentname = "edittedcomment_" . $CommentID;
    $edittedComment = $_POST[$commentname];
    if (!empty($edittedComment)) {
      if ($memberid == $id) {
        $db->Update("UPDATE postcomments SET CommentText = ? WHERE CommentID = ?", array($edittedComment, $CommentID));
      }
    } else {
      $edittedComment = "error";
    }
    echo $edittedComment;
    break;

  case 'deleteComment':
    $id = $_GET['ID'];
    $CommentID = $_GET['CommentID'];
    if ($memberid == $id) {
      $db->Update("UPDATE postcomments SET CommentActive = ? WHERE CommentID = ?", array(0, $CommentID));
      $count_comment = $db->getColumnData("SELECT COUNT(*) FROM postcomments WHERE PostID = ? AND CommentActive = ?", array($postid, 1));
    }
    echo $count_comment;
    break;
}
