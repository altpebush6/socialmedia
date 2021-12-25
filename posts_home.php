<?php

if ($part) {
  $topicName = $db->getColumnData("SELECT TopicName FROM topics WHERE TopicLink = ?", array($part));
  $topicLink = $db->getColumnData("SELECT TopicLink FROM topics WHERE TopicLink = ?", array($part));
  $topicName = "<i>" . $topicName . "</i>";
?>


  <h1 class="header text-light text-center my-5 header-part"><u><?= $topicName ?></u></h1>

<?php } ?>

<div class="container py-4 px-0 px-md-4">
  <div class="create-post border col-md-10 offset-md-1 py-4" style="border-radius: 15px;">
    <form id="form_posting" method="post" enctype="multipart/form-data" class="px-3">
      <div class="row">
        <h3 class="header text-center mb-3 create-post-header" style="font-family: 'Lora', serif;"><?= $translates["createpost"] ?></h3>
      </div>
      <div class="row mt-2">
        <div class="col-2 text-center">
          <img src="images_profile/<?= $profile_photo; ?>" class="rounded-circle" width="50" height="50">
        </div>
        <div class="col-10">
          <?php if ($part) { ?>
            <input type="hidden" value="<?= $part ?>" name="post_part" id="post_part">
            <textarea class="form-control-plaintext text-light" name="text_post" id="text_post" rows="4" cols="80" maxlength="250" placeholder="<?= "#" . $topicLink ?>"></textarea>
          <?php } else { ?>
            <textarea class="form-control-plaintext text-light" name="text_post" id="text_post" rows="4" cols="80" maxlength="250" placeholder="<?= $translates["sharesth"] ?>"></textarea>
          <?php } ?>
          <div class="ps-2 d-flex flex-row" id="review_part" style="overflow:auto;">
            <img id="posting_img" class="mb-3 me-2 rounded-3 w-45">
            <div id="review_more" class="rounded-3 w-25 mb-3 text-light fs-1 border d-none justify-content-center align-items-center" style="background: rgba(0,0,0,0.4);"></div>
          </div>
          <div class="ps-2 my-3 d-flex flex-row text-light" id="reviewfile_part" style="overflow:auto;">
            <span class="pt-1" id="posting_file"></span>
          </div>
          <div class="p-2 text-light border my-3 w-75 d-none" id="warn_file"></div>
        </div>
      </div>
      <div class="row border-top border-bottom py-3 text-light">
        <div class="col-6 fs-5"><?= $translates["addyourpost"] ?></div>
        <div class="col-6 text-end pe-3">
          <input class="d-none" type="file" name="files[]" id="file_upload" accept=".doc,.docx,.pdf" multiple>
          <label for="file_upload" style="cursor:pointer;" class="me-3"><i class="fas fa-file-upload fa-2x"></i></label>
          <input class="d-none" type="file" name="image[]" id="image_upload" accept=image/x-png,image/gif,image/jpeg multiple>
          <label for="image_upload" style="cursor:pointer;"><i class="fas fa-images fa-2x"></i></label>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-10 mx-auto">
          <button type="submit" class="btn btn-primary w-100 rounded-3 border fs-5" name="submitpost" id="submitpost"><?= $translates["shareit"] ?> <span class="spinner" id="spinnershare"></span></button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php

$getMembersPosts = $memberid;
$memberFriends = $db->getDatas("SELECT * FROM friends WHERE FirstMemberID = ? AND FriendRequest = ?", array($memberid, 1));
foreach ($memberFriends as $friend) {
  $getMembersPosts .= "," . $friend->SecondMemberID;
}
$memberFriends2 = $db->getDatas("SELECT * FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($memberid, 1));
foreach ($memberFriends2 as $friend2) {
  $getMembersPosts .= "," . $friend2->FirstMemberID;
}

if ($part) {
  $posts = $db->getDatas("SELECT * FROM posts WHERE PostActive = ? AND PostTopic = ? ORDER BY PostAddTime DESC LIMIT 3", array(1, $part));
} else {
  $posts = $db->getDatas("SELECT * FROM posts WHERE MemberID IN ($getMembersPosts) AND PostClub IS NULL AND PostActive = ? ORDER BY PostAddTime DESC LIMIT 3", array(1));
}

foreach ($posts as $item) {
  $postMemberID = $item->MemberID;
  $isPostownerActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($postMemberID));
  if ($isPostownerActive == 1) {
    $post_ID = $item->PostID;
    $post_text = $item->PostText;
    $post_img = $item->PostImg;
    $post_img2 = $item->PostImg2;
    $post_img3 = $item->PostImg3;
    $post_img4 = $item->PostImg4;
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
    $post_time = $item->PostAddTime;
    $post_topic = $item->PostTopic;
    $texthashtag = "<a href='http://localhost/aybu/socialmedia/" . $translates['home'] . "/" . $post_topic . "' class='text-info text-decoration-none'>#" . $post_topic . "</a>";
    $isfriend = $db->getData("SELECT * FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ? AND FriendRequest = ?", array($memberid, $postMemberID, 1));
    $isfriend2 = $db->getData("SELECT * FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ? AND FriendRequest = ?", array($postMemberID, $memberid, 1));
    $post_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = $item->MemberID");
    $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($item->MemberID));

    if (is_null($post_profile_photo)) {
      if ($gender == 'Erkek') {
        $post_profile_photo = "profilemale.png";
      } else {
        $post_profile_photo = "profilefemale.png";
      }
    }
    $post_user_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = $item->MemberID ");
    $post_user_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = $item->MemberID ");

    $diff_post = calculateTime($post_time);
?>
    <div class="container my-5 px-0 px-md-4" id="<?= $post_ID ?>">
      <div class="border p-3 col-md-10 mx-auto offset-md-1 py-4 post" style="border-radius: 15px;">
        <div class="row mb-3">
          <div class="col-10">
            <a href="http://localhost/aybu/socialmedia/<?= $translates['profile'] ?>/<?= $postMemberID ?>">
              <div class="row justify-content-center">
                <div class="col-2 text-end">
                  <a href="http://localhost/aybu/socialmedia/<?= $translates['profile'] ?>/<?= $postMemberID ?>">
                    <img src="images_profile/<?= $post_profile_photo; ?>" class="rounded-circle" width="50" height="50">
                  </a>
                </div>
                <div class="col-10 ps-3 p-md-0 ">
                  <a class="text-decoration-none text-light" href="http://localhost/aybu/socialmedia/<?= $translates['profile'] ?>/<?= $postMemberID ?>">
                    <?= $post_user_name . " " . $post_user_lastname . "<br><small>" . $diff_post . "</small>"; ?>
                  </a>
                </div>
              </div>
            </a>
          </div>
          <div class="col-2">
            <div class="dropdown-post">
              <button class="dropbtn btn btn-primary rounded-circle"><i class="fas fa-ellipsis-h"></i></button>
              <div class="dropdown-content" style="width:220px;">
                <?php if ($item->MemberID == $memberid) { ?>
                  <a href="javascript:void(0)" onClick="OpenEditPost('<?= $post_ID ?>','<?= $post_text ?>')"><i class="far fa-edit"></i> <?= $translates["editpost"] ?></a>
                  <a href="javascript:void(0)" onClick="DeletePost('deletepost','<?= $memberid ?>','<?= $post_ID ?>')"><i class="far fa-trash-alt"> <?= $translates["deletepost"] ?></i></a>
                  <?php
                } else {
                  $diduRep = $db->getData("SELECT * FROM reports_posts WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $post_ID));
                  if ($diduRep) {
                  ?>
                    <a href="javascript:void(0)" class="text-success unreportPost" postid="<?= $post_ID ?>" id="Report_Post_<?= $post_ID ?>"><i class="fas fa-headset"></i> <?= $translates["reported"] ?></a>
                  <?php } else { ?>
                    <a href="javascript:void(0)" class="text-danger reportPost" postid="<?= $post_ID ?>" id="Report_Post_<?= $post_ID ?>"><i class="fas fa-bug"></i> <?= $translates["reportpost"] ?></a>
                <?php }
                } ?>
              </div>
            </div>
          </div>
        </div>
        <!-- Gönderi Metin, Belge veya Resmi -->
        <div class="text-light text-break fs-6 postmiddle_<?= $post_ID ?>" style="user-select:text" id="postmiddle_<?= $post_ID ?>">
          <span id="post_text_<?= $post_ID ?>" class="ps-4 my-3 <?php echo ($post_text ? "d-block" : "d-none") ?>"><?php echo ($post_topic ? $texthashtag . " " . $post_text : $post_text) ?></span>
          <div class="row d-flex flex-column ps-3" id="post_files_<?= $post_ID ?>">

            <?php

            for ($i = 1; $i < 5; $i++) {
              if ($i > 1) {
                $postfile = "PostFile" . $i;
                if ($item->$postfile) {
                  echo '<div class="col-12 my-2 ps-4 fs-6"><a class="text-light" href="http://localhost/aybu/socialmedia/' . $translates["home"] . '?download=' . $item->$postfile . '"><i class="fas fa-file-alt fa-2x"></i> ' . $item->$postfile . '</a></div>';
                }
              } else {
                if ($item->PostFile) {
                  echo '<div class="col-12 my-2 ps-4 fs-6"><a class="text-light" href="http://localhost/aybu/socialmedia/' . $translates["home"] . '?download=' . $item->PostFile . '"><i class="fas fa-file-alt fa-2x"></i> ' . $item->PostFile . '</a></div>';
                }
              }
            }

            ?>

          </div>
          <div class="d-flex flex-row p-0 m-0">
            <div class="row w-100 ps-4" id="post_images_<?= $post_ID ?>">
              <?php if (!is_null($post_img)) {
                switch ($img_counter) {
                  case 1: ?>
                    <a href="post_images/<?= $post_img ?>" class="col-12">
                      <img src="post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
                  <?php break;
                  case 2: ?>
                    <a href="post_images/<?= $post_img ?>" class="col-6 pe-1">
                      <img src="post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
                    <a href="post_images/<?= $post_img2 ?>" class="col-6 pe-1">
                      <img src="post_images/<?= $post_img2 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
                  <?php break;
                  case 3: ?>
                    <a href="post_images/<?= $post_img ?>" class="col-6 pe-1">
                      <img src="post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
                    <a href="post_images/<?= $post_img2 ?>" class="col-6 pe-1">
                      <img src="post_images/<?= $post_img2 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
                    <a href="post_images/<?= $post_img3 ?>" class="col-6 pe-1">
                      <img src="post_images/<?= $post_img3 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
                  <?php break;
                  case 4: ?>
                    <a href="post_images/<?= $post_img ?>" class="col-6 pe-1">
                      <img src="post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
                    <a href="post_images/<?= $post_img2 ?>" class="col-6 ps-1">
                      <img src="post_images/<?= $post_img2 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
                    <a href="post_images/<?= $post_img3 ?>" class="col-6 pe-1">
                      <img src="post_images/<?= $post_img3 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
                    <a href="post_images/<?= $post_img4 ?>" class="col-6 ps-1">
                      <img src="post_images/<?= $post_img4 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                    </a>
              <?php break;
                }
              } ?>
              <script>
                baguetteBox.run('.postmiddle_<?= $post_ID ?>');
              </script>
            </div>
          </div>
        </div>
        <!-- Gönderi Düzenleme -->
        <div class="d-none" id="addpartul_<?= $post_ID ?>">
          <form id='form_edit<?= $post_ID ?>' class='form_edit' idsi="<?= $post_ID ?>" method='post' enctype='multipart/form-data'>
            <input autocomplete="off" type='text' class="form-control-plaintext text-light" name='edittedtext' id='edittedtext_<?= $post_ID ?>' value='<?= $post_text ?>' class='posttext-input' style="margin-bottom:5%;width:100%;padding-left: 2%;">
            <div class="row d-flex flex-column ps-3" id="edit_post_files_<?= $post_ID ?>">

              <?php

              for ($i = 1; $i < 5; $i++) {
                if ($i > 1) {
                  $postfile = "PostFile" . $i;
                  if ($item->$postfile) {
                    echo '<div class="col-12 text-light my-2 ps-4 fs-6"><i class="fas fa-file-alt fa-2x"></i> <a class="text-light" href="">' . $item->$postfile . '</a></div>';
                  }
                } else {
                  if ($item->PostFile) {
                    echo '<div class="col-12 text-light my-2 ps-4 fs-6"><i class="fas fa-file-alt fa-2x"></i> <a class="text-light" href="">' . $item->PostFile . '</a></div>';
                  }
                }
              }

              ?>

            </div>
            <div class="ps-2 d-flex flex-row" id="review_part_edit_<?= $post_ID ?>" style="overflow:auto;">
              <img id="posting_img_edit_<?= $post_ID ?>" class="mb-3 me-2 rounded-3 w-45">
              <div id="review_more_edit_<?= $post_ID ?>" class="rounded-3 w-25 mb-3 text-light fs-1 border d-none justify-content-center align-items-center" style="background: rgba(0,0,0,0.4);"></div>
            </div>
            <div class="p-2 text-light border my-3 w-75 d-none" id="warn_file_edit_<?= $post_ID ?>"></div>
            <div class="d-flex flex-row edit_post_images_<?= $post_ID ?>" id="edit_post_images_<?= $post_ID ?>">
              <div class="row">
                <?php if (!is_null($post_img)) {
                  switch ($img_counter) {
                    case 1: ?>
                      <a href="post_images/<?= $post_img ?>" class="col-12" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                    <?php break;
                    case 2: ?>
                      <a href="post_images/<?= $post_img ?>" class="col-6 pe-1" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                      <a href="post_images/<?= $post_img2 ?>" class="col-6 pe-1" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img2 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                    <?php break;
                    case 3: ?>
                      <a href="post_images/<?= $post_img ?>" class="col-6 pe-1" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                      <a href="post_images/<?= $post_img2 ?>" class="col-6 pe-1" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img2 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                      <a href="post_images/<?= $post_img3 ?>" class="col-6 pe-1" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img3 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                    <?php break;
                    case 4: ?>
                      <a href="post_images/<?= $post_img ?>" class="col-6 pe-1" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                      <a href="post_images/<?= $post_img2 ?>" class="col-6 pe-1" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img2 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                      <a href="post_images/<?= $post_img3 ?>" class="col-6 pe-1" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img3 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                      <a href="post_images/<?= $post_img4 ?>" class="col-6 pe-1" class="editting_img_<?= $post_ID ?>">
                        <img src="post_images/<?= $post_img4 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                      </a>
                <?php break;
                  }
                } ?>
                <script>
                  baguetteBox.run('.edit_post_images_<?= $post_ID ?>');
                </script>
              </div>
            </div>
            <div class="row border-top border-bottom py-3 text-light">
              <div class="col-6 fs-7"><?= $translates["addyourpost"] ?></div>
              <div class="col-6 text-end">
                <input class="d-none edit_file_upload" postid="<?= $post_ID ?>" type="file" name="edit_file_upload[]" id="edit_file_upload<?= $post_ID ?>" accept=.doc,.docx,.pdf multiple>
                <label for="edit_file_upload<?= $post_ID ?>" style="cursor:pointer;" class="me-3"><i class="fas fa-file-upload" style="font-size:21px"></i></label>
                <input class="d-none edit_image_upload" postid="<?= $post_ID ?>" type="file" name="edit_image_upload[]" id="edit_image_upload<?= $post_ID ?>" accept=image/x-png,image/gif,image/jpeg multiple>
                <label for="edit_image_upload<?= $post_ID ?>" style="cursor:pointer;"><i class="fas fa-images" style="font-size:21px"></i></label>
              </div>
            </div>
            <div class="row my-3">
              <div class="col-10 mx-auto">
                <button type="submit" class="btn btn-primary w-100 rounded-3 border fs-5 saveedit" name='saveedit' id='saveedit<?= $post_ID ?>'><?= $translates["shareit"] ?> <span class="spinner" id="spinnershare"></span></button>
              </div>
            </div>
          </form>
        </div>
        <!-- Gönderi Beğeni Yorum -->
        <div class="row py-2 mt-2" id="likecomment_<?= $post_ID ?>">
          <div class="col-6 text-center text-light border-end" id="like_<?= $post_ID ?>">
            <?php
            $is_liked = $db->getColumnData("SELECT * FROM postlike WHERE PostID = ? AND MemberID = ? ", array($post_ID, $memberid));
            $count_like = $db->getColumnData("SELECT COUNT(*) FROM postlike WHERE PostID = ?", array($post_ID));
            $count_comment = $db->getColumnData("SELECT COUNT(*) FROM postcomments WHERE PostID = ? AND CommentActive = ?", array($post_ID, 1));
            if (empty($is_liked)) { ?>
              <a class="text-decoration-none text-light" onClick="Like('increaseLike','<?= $post_ID ?>')">
                <i class="far fa-thumbs-up" style="cursor:pointer;"></i>
                <label style="cursor:pointer;"> <?= $translates["likepost"] ?> (<?= $count_like ?>)</label>
              </a>
            <?php } else { ?>
              <a class="text-info text-decoration-none" onClick="Like('decreaseLike','<?= $post_ID ?>')">
                <i class="far fa-thumbs-down" style="cursor:pointer;"></i>
                <label style="cursor:pointer;"> <?= $translates["dislikepost"] ?> (<?= $count_like ?>)</label>
              </a>
            <?php } ?>
          </div>

          <div class="col-6 text-center text-light" id="comment_<?= $post_ID ?>">
            <a onClick="openComments(<?= $post_ID ?>)">
              <i class="far fa-comment-alt" style="cursor:pointer;"></i>
              <label style="cursor:pointer;" id="comment_label_<?= $post_ID ?>"> <?= $translates["commentpost"] ?> (<?= $count_comment ?>)</label>
            </a>
          </div>
        </div>
        <!-- Gönderiye Yorum Yap -->
        <div class="row align-items-center p-3 border-top border-bottom" style="display:none" id="postcomment_<?= $post_ID ?>">
          <div class="col-1"><img src="images_profile/<?= $profile_photo; ?>" class="rounded-circle" width="40" height="40"></div>
          <div class="col-11 ps-4 ps-md-2">
            <form method="post" id="form_comment_<?= $post_ID ?>">
              <div class="row align-items-center">
                <div class="col-11 text-center">
                  <input class="create-comment form-control form-control-sm rounded-3" type="text" maxlength="100" name="text_<?= $post_ID ?>" id="text_<?= $post_ID ?>" placeholder="<?= $translates["saysth"] ?>">
                </div>
                <div class="col-1 p-0 m-0">
                  <button type="button" class="btn btn-outline-light btn-sm rounded-3" name="submittext_<?= $post_ID ?>" id="submittext_<?= $post_ID ?>" onClick="Comment('sharecomment','<?= $post_ID ?>')">
                    <i class="far fa-paper-plane"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- Gönderi Yorumları -->
        <div class="row p-2 justify-content-center" style="display:none;user-select:text" id="comments_<?= $post_ID ?>">
          <?php
          $comments = $db->getDatas("SELECT * FROM postcomments WHERE PostID = ? AND CommentActive = ?", array($post_ID, 1));
          foreach ($comments as $postinfo) {
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
          ?>
            <div class="row text-light px-3 py-1 mt-md-2" id="each_comment_<?= $comment_ID ?>">
              <div class="col-1 p-0 pt-2 d-flex align-items-start justify-content-start">
                <a href="http://localhost/aybu/socialmedia/<?= $translates['profile'] ?>/<?= $postinfo->MemberID ?>">
                  <img src="images_profile/<?= $comment_profile_photo; ?>" class="rounded-circle" width="40" height="40">
                </a>
              </div>
              <div class="col-9 ps-4 p-md-0">
                <div class="col-12">
                  <a class="text-light text-decoration-none" href="http://localhost/aybu/socialmedia/<?= $translates['profile'] ?>/<?= $postinfo->MemberID ?>">
                    <small>
                      <!-- İsim - süre -->
                      <?php
                      $comment_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = $postinfo->MemberID");
                      $comment_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = $postinfo->MemberID");
                      echo $comment_name . " " . $comment_lastname . " - ";

                      $comment_time = $postinfo->CommentAddTime;
                      $diff_comment = calculateTime($comment_time);
                      echo $diff_comment;
                      ?>
                    </small>
                  </a>
                </div>
                <div class="col-12 text-break">
                  <p class="m-0" id="comment_text_<?= $comment_ID ?>"><?= $commentText ?></p>
                  <form class="d-none" method="post" id="form_editcomment_<?= $comment_ID ?>">
                    <div class="row align-items-center">
                      <div class="col-10 text-center">
                        <input autocomplete="off" class="create-comment form-control form-control-sm rounded-3" type="text" maxlength="100" value="<?= $commentText ?>" name="edittedcomment_<?= $comment_ID ?>" id="edittedcomment_<?= $comment_ID ?>" placeholder="<?= $translates["saysth"] ?>">
                      </div>
                      <div class="col-1 p-0 m-0">
                        <button type="button" class="btn btn-outline-light btn-sm rounded-3" onClick="CommentOperate('editComment','<?= $post_ID ?>','<?= $comment_ID ?>')">
                          <i class="far fa-paper-plane"></i>
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <div class="col-2 text-end p-0">
                <?php if ($postinfo->MemberID == $memberid) { ?>
                  <small>
                    <i class="icon-edit fas fa-pencil-alt me-2" style="cursor:pointer;" id="editComment" onClick="OpenEditComment('<?= $comment_ID ?>','<?= $post_ID ?>')"></i>
                    <i class="icon-delete fas fa-trash-alt" style="cursor:pointer;" id="deleteComment" onClick="CommentOperate('deleteComment','<?= $post_ID ?>','<?= $comment_ID ?>')"></i>
                  </small>
                <?php } else { ?>
                  <small id="Report_Comment_<?= $comment_ID ?>">
                    <?php
                    $diduRep = $db->getData("SELECT * FROM reports_comments WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $comment_ID));
                    if ($diduRep) {
                    ?>
                      <i class="fas fa-headset delreportcomment text-success delreportitem_<?= $comment_ID ?>" onClick="DelReportComment('<?= $comment_ID ?>')"></i>
                    <?php } else { ?>
                      <i class="fas fa-bug reportcomment text-danger reportitem_<?= $comment_ID ?>" onClick="ReportComment('<?= $comment_ID ?>')"></i>
                    <?php } ?>
                  </small>
                <?php }
                ?>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
<?php
  }
}
$event = $db->getData("SELECT * FROM events WHERE EventPremium = ? ORDER BY EventID DESC", array(1));
$eventOrganizer = $db->getData("SELECT * FROM members WHERE MemberID = ?", array($event->EventOrganizerID));
$event_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($event->EventOrganizerID));
$organizerGender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($event->EventOrganizerID));

if (is_null($event_profile_photo)) {
  if ($organizerGender == 'Erkek') {
    $event_profile_photo = "profilemale.png";
  } else {
    $event_profile_photo = "profilefemale.png";
  }
}
$eventHeader = $event->EventHeader;
?>
<a class="text-decoration-none" href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>/<?= seolink($event->EventHeader) . "-" . $event->EventID ?>">
  <div class="container-event my-5 px-0 px-md-4" eventid="<?= $event->EventID ?>">
    <div class="border offset-md-1 col-md-10 mx-auto p-3 bg-dark mx-auto py-4 post eventPre" style="border-radius: 15px;">
      <div class="ribbon"><span>GOLD</span></div>
      <div class="text-light text-break fs-5 eventmiddle_<?= $event->EventID ?>" style="user-select:text" id="eventmiddle_<?= $event->EventID ?>">
        <h6 id="event_header_<?= $event->EventID ?>" class="ps-4 my-3 fs-2 text-center eventHeader d-block"><?= $eventHeader ?></h6>
        <div class="d-flex flex-row p-0 m-0">
          <div class="row w-100 ps-4" id="event_image_<?= $event->EventID ?>">
            <a href="events_images/<?= $event->EventImage ?>" class="col-12 pe-1">
              <img src="events_images/<?= $event->EventImage ?>" style="width:100%;border-radius:5px;margin-top:15px;">
            </a>
            <script>
              baguetteBox.run('.eventmiddle_<?= $event->EventID ?>');
            </script>
          </div>
        </div>
      </div>
    </div>
  </div>
</a>