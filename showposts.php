<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";
require_once "functions/time.php";

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

$result = array();

$id = security("id");
$part = security("part");
$memberid = $SS->get("MemberID");
$from = $_GET["From"];

$getMembersPosts = $memberid;
$memberFriends = $db->getDatas("SELECT * FROM friends WHERE FirstMemberID = ? AND FriendRequest = ?", array($memberid, 1));
foreach ($memberFriends as $friend) {
  $getMembersPosts .= "," . $friend->SecondMemberID;
}
$memberFriends2 = $db->getDatas("SELECT * FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($memberid, 1));
foreach ($memberFriends2 as $friend2) {
  $getMembersPosts .= "," . $friend2->FirstMemberID;
}

if ($from == "Home") {
  if (!$part) {
    $posts = $db->getDatas("SELECT * FROM posts WHERE PostID < $id AND PostActive = ? ORDER BY PostAddTime DESC LIMIT 3", array(1));
  } else {
    $posts = $db->getDatas("SELECT * FROM posts WHERE PostID < $id AND MemberID IN ($getMembersPosts) AND PostTopic = ? AND PostActive = ? ORDER BY PostAddTime DESC LIMIT 3", array($part, 1));
  }
} elseif ($from == "Profile") {
  if ($part) {
    $posts = $db->getDatas("SELECT * FROM posts WHERE PostID < $id AND MemberID = ? AND PostActive = ? ORDER BY PostAddTime DESC LIMIT 3", array($part, 1));
  } else {
    $posts = $db->getDatas("SELECT * FROM posts WHERE PostID < $id AND MemberID = ? AND PostActive = ? ORDER BY PostAddTime DESC LIMIT 3", array($memberid, 1));
  }
} elseif ($from == "Clubs") {
  $posts = $db->getDatas("SELECT * FROM posts WHERE PostID < $id AND PostClub = ? AND PostActive = ? ORDER BY PostAddTime DESC LIMIT 3", array($part, 1));
  $ClubPresidentID = $db->getColumnData("SELECT ClubPresidentID FROM clubs WHERE ClubID = ?", array($part));
  if ($ClubPresidentID == $memberid) {
    $clubpresident = $ClubPresidentID;
  }
}

$result["posts"] = $posts;

$counter = count($posts);

sleep(0.5);

if ($counter > 0) {
  $result["where"] = $part;
  foreach ($posts as $post) {
    $postMemberID = $post->MemberID;
    $postID = $post->PostID;
    $post_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = $post->MemberID");
    $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($post->MemberID));

    if (is_null($post_profile_photo)) {
      if ($gender == 'Erkek') {
        $post_profile_photo = "profilemale.png";
      } else {
        $post_profile_photo = "profilefemale.png";
      }
    }
    $post_topic = $post->PostTopic;
    $texthashtag = "<a href='http://localhost/aybu/socialmedia/" . $translates['home'] . "/" . $post_topic . "' class='text-info text-decoration-none'>#" . $post_topic . "</a>";
    $post_img = $post->PostImg;
    $post_img2 = $post->PostImg2;
    $post_img3 = $post->PostImg3;
    $post_img4 = $post->PostImg4;
    $img_counter = 1;
    if ($post_img2) {
      $img_counter = 2;
    }
    if ($post_img3) {
      $img_counter = 3;
    }
    if ($post_img4) {
      $img_counter = 4;
    }
    $post_user_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = $post->MemberID");
    $post_user_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = $post->MemberID");
    $post_time = $post->PostAddTime;
    $diff_post = calculateTime($post_time);
    $profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberid));
    if (is_null($profile_photo)) {
      if ($gender == 'Erkek') {
        $profile_photo = "profilemale.png";
      } else {
        $profile_photo = "profilefemale.png";
      }
    }

    $result["state"] .= '<div class="container my-5 px-0 px-md-4" id="' . $post->PostID . '">
                                <div class="border rounded-1 p-3 col-md-10 offset-md-1 py-4 post">
                                    <div class="row mb-3">
                                        <div class="col-10">
                                            <a href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $post->MemberID . '">
                                                <div class="row justify-content-center">
                                                    <div class="col-2 text-end">
                                                        <a href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $post->MemberID . '">
                                                            <img src="images_profile/' . $post_profile_photo . '" class="rounded-circle" width="50" height="50">
                                                        </a>
                                                    </div>
                                                    <div class="col-10 ps-3 p-md-0 ">
                                                        <a class="text-decoration-none text-light" href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $post->MemberID . '">
                                                            ' . $post_user_name . " " . $post_user_lastname . "<br><small>" . $diff_post . "</small>" . '
                                                        </a>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <div class="col-2">
                                <div class="dropdown-post">
                                    <button class="dropbtn btn btn-primary rounded-circle"><i class="fas fa-ellipsis-h"></i></button>
                                    <div class="dropdown-content" style="width:220px;">';

    if ($postMemberID == $memberid) {
      $result["state"] .= '<a href="javascript:void(0)" onClick=\'OpenEditPost("' . $post->PostID . '","' . $post->PostText . '")\'><i class="far fa-edit"></i> ' . $translates["editpost"] . '</a>
                                                             <a href="javascript:void(0)" onClick=\'DeletePost("deletepost","' . $memberid . '","' . $post->PostID . '")\'><i class="far fa-trash-alt"> ' . $translates["deletepost"] . '</i></a>';
    } elseif ($clubpresident) {
      $result["state"] .= '<a href="javascript:void(0)" onClick=\'DeletePost("deletepost","' . $memberid . '","' . $post->PostID . '")\'><i class="far fa-trash-alt"> ' . $translates["deletepost"] . '</i></a>';
    } else {
      $diduRep = $db->getData("SELECT * FROM reports_posts WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $post->PostID));
      if ($diduRep) {
        $result["state"] .= '<a href="javascript:void(0)" class="text-success unreportPost" postid="' . $post->PostID . '" id="Report_Post_' . $post->PostID . '"><i class="fas fa-headset"></i> ' . $translates["reported"] . '</a>';
      } else {
        $result["state"] .= '<a href="javascript:void(0)" class="text-danger reportPost" postid="' . $post->PostID . '" id="Report_Post_' . $post->PostID . '"><i class="fas fa-bug"></i> ' . $translates["reportpost"] . '</a>';
      }
    }
    $result["state"] .= '</div>
                                  </div>
                                </div>
                              </div>
                              <div class="text-light d-flex flex-column text-break fs-6 postmiddle_' . $post->PostID . '" style="user-select:text" id="postmiddle_' . $post->PostID . '">
                                <span id="post_text_' . $post->PostID . '" class="ps-4 my-3">';
    if ($post_topic) {
      $result["state"] .= $texthashtag . ' ' . $post->PostText;
    } else {
      $result["state"] .= $post->PostText;
    }
    $result["state"] .= '</span>';
    $result["state"] .= '<div class="row d-flex flex-column ps-3" id="post_files_' . $post->PostID . '">';

    for ($i = 1; $i < 5; $i++) {
      if ($i > 1) {
        $postfile = "PostFile" . $i;
        if ($post->$postfile) {
          $result["state"] .= '<div class="col-12 my-2 ps-4 fs-6"><i class="fas fa-file-alt fa-2x"></i> <a class="text-light" href="">' . $post->$postfile . '</a></div>';
        }
      } else {
        if ($post->PostFile) {
          $result["state"] .= '<div class="col-12 my-2 ps-4 fs-6"><i class="fas fa-file-alt fa-2x"></i> <a class="text-light" href="">' . $post->PostFile . '</a></div>';
        }
      }
    }
    $result["state"] .= '</div><div class="d-flex flex-row p-0 m-0">
        <div class="row w-100 ps-4" id="post_images_' . $post->PostID . '">';
    if (!is_null($post_img)) {
      switch ($img_counter) {
        case 1:
          $result["state"] .= '<a href="post_images/' . $post_img . '" class="col-12">
                                  <img src="post_images/' . $post_img . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>';
          break;
        case 2:
          $result["state"] .= '<a href="post_images/' . $post_img . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img2 . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img2 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>';
          break;
        case 3:
          $result["state"] .= '<a href="post_images/' . $post_img . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img2 . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img2 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img3 . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img3 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>';
          break;
        case 4:
          $result["state"] .= '<a href="post_images/' . $post_img . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img2 . '" class="col-6 ps-1">
                                  <img src="post_images/' . $post_img2 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img3 . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img3 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img4 . '" class="col-6 ps-1">
                                  <img src="post_images/' . $post_img4 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>';
          break;
      }
    }

    $result["state"] .= '<script>
                            baguetteBox.run(".postmiddle_' . $post->PostID . '");
                          </script></div></div></div>
                                <div class="d-none" id="addpartul_' . $post->PostID . '">
                                  <form id="form_edit' . $post->PostID . '" class="form_edit" idsi="' . $post->PostID . '" method="post" enctype="multipart/form-data">
                                    <input autocomplete="off" type="text" class="form-control-plaintext text-light" name="edittedtext" id="edittedtext_' . $post->PostID . '" value="' . $post->PostText . '" class="posttext-input" style="width:100%;padding-left: 2%;">';
    $result["state"] .= '<div class="row d-flex flex-column ps-3" id="edit_post_files_' . $post->PostID . '">';

    for ($i = 1; $i < 5; $i++) {
      if ($i > 1) {
        $postfile = "PostFile" . $i;
        if ($item->$postfile) {
          $result["state"] .= '<div class="col-12 text-light my-2 ps-4 fs-6"><i class="fas fa-file-alt fa-2x"></i> <a class="text-light" href="">' . $item->$postfile . '</a></div>';
        }
      } else {
        if ($item->PostFile) {
          $result["state"] .= '<div class="col-12 text-light my-2 ps-4 fs-6"><i class="fas fa-file-alt fa-2x"></i> <a class="text-light" href="">' . $item->PostFile . '</a></div>';
        }
      }
    }


    $result["state"] .= '</div><div class="ps-2 d-flex flex-row" id="review_part_edit_' . $post->PostID . '" style="overflow:auto;">
                                      <img id="posting_img_edit_' . $post->PostID . '" class="mb-3 me-2 rounded-3 w-45">
                                      <div id="review_more_edit_' . $post->PostID . '" class="rounded-3 w-25 mb-3 text-light fs-1 border d-none justify-content-center align-items-center" style="background: rgba(0,0,0,0.4);"></div>
                                    </div>
                                    <div class="p-2 text-light border my-3 w-75 d-none" id="warn_file_edit_' . $post->PostID . '"></div>
                                    <div class="d-flex flex-row edit_post_images_' . $post->PostID . '" id="edit_post_images_' . $post->PostID . '">
                                      <div class="row">';
    if (!is_null($post_img)) {
      switch ($img_counter) {
        case 1:
          $result["state"] .= '<a href="post_images/' . $post_img . '" class="col-12">
                                  <img src="post_images/' . $post_img . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>';
          break;
        case 2:
          $result["state"] .= '<a href="post_images/' . $post_img . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                  <a href="post_images/' . $post_img2 . '" class="col-6 pe-1">
                                <img src="post_images/' . $post_img2 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>';
          break;
        case 3:
          $result["state"] .= '<a href="post_images/' . $post_img . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img2 . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img2 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img3 . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img3 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>';
          break;
        case 4:
          $result["state"] .= '<a href="post_images/' . $post_img . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img2 . '" class="col-6 ps-1">
                                  <img src="post_images/' . $post_img2 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img3 . '" class="col-6 pe-1">
                                  <img src="post_images/' . $post_img3 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>
                                <a href="post_images/' . $post_img4 . '" class="col-6 ps-1">
                                  <img src="post_images/' . $post_img4 . '" style="width:100%;border-radius:5px;margin-top:15px;">
                                </a>';
          break;
      }
    }
    $result["state"] .= '<script>
                            baguetteBox.run(".edit_post_images_' . $post->PostID . '");
                          </script></div></div>
                                    <div class="row border-top border-bottom py-3 text-light">
                                      <div class="col-6 fs-7">' . $translates["addyourpost"] . '</div>
                                      <div class="col-6 text-end">
                                        <input class="d-none edit_file_upload" postid="' . $post->PostID . '" type="file" name="edit_file_upload[]" id="edit_file_upload' . $post->PostID . '" accept=.doc,.docx,.pdf multiple>
                                        <label for="edit_file_upload' . $post->PostID . '" style="cursor:pointer;" class="me-3"><i class="fas fa-file-upload" style="font-size:21px"></i></label>
                                        <input class="d-none edit_image_upload" postid="' . $post->PostID . '" type="file" name="edit_image_upload[]" id="edit_image_upload' . $post->PostID . '" accept=image/x-png,image/gif,image/jpeg multiple>
                                        <label for="edit_image_upload' . $post->PostID . '" style="cursor:pointer;"><i class="fas fa-images" style="font-size:21px"></i></label>
                                      </div>
                                    </div>
                                    <div class="row my-3">
                                      <div class="col-10 mx-auto">
                                        <button type="submit" class="btn btn-primary w-100 rounded-3 border fs-5 saveedit" name="saveedit" id="saveedit' . $post->PostID . '">' . $translates["shareit"] . ' <span class="spinner" id="spinnershare"></span></button>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                                <div class="row py-2 mt-2" id="likecomment_' . $post->PostID . '">
                                  <div class="col-6 text-center text-light border-end" id="like_' . $post->PostID . '">';
    $is_liked = $db->getColumnData("SELECT * FROM postlike WHERE PostID = ? AND MemberID = ? ", array($post->PostID, $memberid));
    $count_like = $db->getColumnData("SELECT COUNT(*) FROM postlike WHERE PostID = ?", array($post->PostID));
    $count_comment = $db->getColumnData("SELECT COUNT(*) FROM postcomments WHERE PostID = ? AND CommentActive = ?", array($post->PostID, 1));
    if (empty($is_liked)) {
      $result["state"] .= '<a class="text-decoration-none text-light" onClick=\'Like("increaseLike","' . $post->PostID . '")\'>
                                        <i class="far fa-thumbs-up" style="cursor:pointer;"></i>
                                        <label style="cursor:pointer;"> ' . $translates["likepost"] . ' (' . $count_like . ')</label>
                                      </a>';
    } else {
      $result["state"] .= '<a class="text-info text-decoration-none" onClick=\'Like("decreaseLike","' . $post->PostID . '")\'>
                                        <i class="far fa-thumbs-down" style="cursor:pointer;"></i>
                                        <label style="cursor:pointer;"> ' . $translates["dislikepost"] . ' (' . $count_like . ')</label>
                                      </a>';
    }
    $result["state"] .= '</div>
                                  <div class="col-6 text-center text-light" id="comment_' . $post->PostID . '">
                                    <a onClick="openComments(' . $post->PostID . ')">
                                      <i class="far fa-comment-alt" style="cursor:pointer;"></i>
                                      <label style="cursor:pointer;" id="comment_label_' . $post->PostID . '"> ' . $translates["commentpost"] . ' (' . $count_comment . ')</label>
                                    </a>
                                  </div>
                                </div>
                                <div class="row align-items-center p-3 border-top border-bottom" style="display:none" id="postcomment_' . $post->PostID . '">
                                  <div class="col-1"><img src="images_profile/' . $profile_photo . '" class="rounded-circle" width="40" height="40"></div>
                                  <div class="col-11 ps-4 ps-md-2">
                                    <form method="post" id="form_comment_' . $post->PostID . '">
                                      <div class="row align-items-center">
                                        <div class="col-11 text-center">
                                          <input class="create-comment form-control form-control-sm rounded-3" type="text" maxlength="100" name="text_' . $post->PostID . '" id="text_' . $post->PostID . '" placeholder="' . $translates["saysth"] . '">
                                        </div>
                                        <div class="col-1 p-0 m-0">
                                          <button type="button" class="btn btn-outline-light btn-sm rounded-3" name="submittext_' . $post->PostID . '" id="submittext_' . $post->PostID . '" onClick=\'Comment("sharecomment","' . $post->PostID . '")\'>
                                            <i class="far fa-paper-plane"></i>
                                          </button>
                                        </div>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                                <div class="row p-2 justify-content-center" style="display:none;user-select:text" id="comments_' . $post->PostID . '">';
    $allcomments = $db->getDatas("SELECT * FROM postcomments WHERE PostID = ? AND CommentActive = ?", array($postID, 1));
    foreach ($allcomments as $postinfo) {
      $comment_ID = $postinfo->CommentID;
      $commentText = $postinfo->CommentText;
      $comment_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = $postinfo->MemberID");

      $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($postinfo->MemberID));

      if (is_null($comment_profile_photo)) {
        if ($gender == 'Erkek') {
          $comment_profile_photo = "profilemale.png";
        } else {
          $comment_profile_photo = "profilefemale.png";
        }
      }
      $result["state"] .= '<div class="row text-light px-3 py-1 mt-md-2" id="each_comment_' . $comment_ID . '">
                                      <div class="col-1 p-0 pt-2 d-flex align-items-start justify-content-start">
                                        <a href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $postinfo->MemberID . '">
                                          <img src="images_profile/' . $comment_profile_photo . '" class="rounded-circle" width="40" height="40">
                                        </a>
                                      </div>
                                      <div class="col-9 ps-4 p-md-0">
                                        <div class="col-12">
                                          <a class="text-light text-decoration-none" href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $postinfo->MemberID . '">
                                            <small>';
      $comment_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = $postinfo->MemberID");
      $comment_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = $postinfo->MemberID");
      $result["state"] .= $comment_name . ' ' . $comment_lastname . ' - ';

      $comment_time = $postinfo->CommentAddTime;
      $diff_comment = calculateTime($comment_time);
      $result["state"] .= $diff_comment . '
                                            </small>
                                          </a>
                                        </div>
                                        <div class="col-12 text-break">
                                          <p class="m-0" id="comment_text_' . $comment_ID . '">' . $commentText . '</p>
                                          <form class="d-none" method="post" id="form_editcomment_' . $comment_ID . '">
                                            <div class="row align-items-center">
                                              <div class="col-10 text-center">
                                                <input autocomplete="off" class="create-comment form-control form-control-sm rounded-3" type="text" maxlength="100" value="' . $commentText . '" name="edittedcomment_' . $comment_ID . '" id="edittedcomment_' . $comment_ID . '" placeholder="' . $translates["saysth"] . '">
                                              </div>
                                              <div class="col-1 p-0 m-0">
                                                <button type="button" class="btn btn-outline-light btn-sm rounded-3" onClick=\'CommentOperate("editComment","' . $post->PostID . '","' . $comment_ID . '")\'>
                                                  <i class="far fa-paper-plane"></i>
                                                </button>
                                              </div>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                      <div class="col-2 text-end p-0">';
      if ($postinfo->MemberID == $memberid) {
        $result["state"] .= '<small>
                                            <i class="icon-edit fas fa-pencil-alt me-2" style="cursor:pointer;" id="editComment" onClick=\'OpenEditComment("' . $comment_ID . '","' . $post->PostID . '")\'></i>
                                            <i class="icon-delete fas fa-trash-alt" style="cursor:pointer;" id="deleteComment" onClick=\'CommentOperate("deleteComment","' . $post->PostID . '","' . $comment_ID . '")\'></i>
                                          </small>';
      } else {
        $result["state"] .= '<small id="Report_Comment_' . $comment_ID . '">';
        $diduRep = $db->getData("SELECT * FROM reports_comments WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $comment_ID));
        if ($diduRep) {
          $result["state"] .= '<i class="fas fa-headset delreportcomment text-success delreportitem_' . $comment_ID . '" onClick=\'DelReportComment("' . $comment_ID . '")\'"></i>';
        } else {
          $result["state"] .= '<i class="fas fa-bug reportcomment text-danger reportitem_' . $comment_ID . '" onClick=\'ReportComment("' . $comment_ID . '")\'></i>';
        }
        $result["state"] .= '</small>';
      }
      $result["state"] .= '</div>
                                    </div>';
    }
    $result["state"] .= '</div>
                              </div>
                            </div>';
  }
} else {
  $result["state"] = "empty";
}

echo json_encode($result);
