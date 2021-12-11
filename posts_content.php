<?php

$posts = $db->getDatas("SELECT * FROM posts WHERE PostActive = ? ORDER BY PostAddTime DESC", array(1));

foreach ($posts as $item) {
  $postMemberID = $item->MemberID;
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
  $texthashtag = "<a href='' class='text-info text-decoration-none'>#" . $post_topic . "</a>";
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
  <div class="container my-5 px-0 px-md-4" id="postid_<?= $post_ID ?>">
    <div class="border rounded-1 p-3 col-md-10 offset-md-1 py-4 post">
      <div class="row mb-3">
        <div class="col-10">
          <a href="">
            <div class="row justify-content-center">
              <div class="col-1 text-end">
                <a href="">
                  <img src="../socialmedia/images_profile/<?= $post_profile_photo; ?>" class="rounded-circle" width="50" height="50">
                </a>
              </div>
              <div class="col-11 ps-5">
                <a class="text-decoration-none text-dark" href="">
                  <?= $post_user_name . " " . $post_user_lastname . "<br><small>" . $diff_post . "</small>"; ?>
                </a>
              </div>
            </div>
          </a>
        </div>
        <div class="col-2">
          <a href="javascript:void(0)" onClick="DeletePost('deletepost','<?= $post_ID ?>')" class="fs-4 text-danger"><i class="far fa-trash-alt"></i></a>
        </div>
      </div>
      <!-- Gönderi Metin veya Resmi -->
      <div class="text-dark text-break fs-6 postmiddle_<?= $post_ID ?>" style="user-select:text" id="postmiddle_<?= $post_ID ?>">
        <span id="post_text_<?= $post_ID ?>"><?php echo ($post_topic ? $texthashtag . " " . $post_text : $post_text) ?></span>
        <div class="d-flex flex-row p-0 m-0">
          <div class="row w-100 ps-4" id="post_images_<?= $post_ID ?>">
            <?php if (!is_null($post_img)) {
              switch ($img_counter) {
                case 1: ?>
                  <a href="../socialmedia/post_images/<?= $post_img ?>" class="col-12">
                    <img src="../socialmedia/post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                  </a>
                <?php break;
                case 2: ?>
                  <a href="../socialmedia/post_images/<?= $post_img ?>" class="col-6 pe-1">
                    <img src="../socialmedia/post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                  </a>
                  <a href="../socialmedia/post_images/<?= $post_img2 ?>" class="col-6 pe-1">
                    <img src="../socialmedia/post_images/<?= $post_img2 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                  </a>
                <?php break;
                case 3: ?>
                  <a href="../socialmedia/post_images/<?= $post_img ?>" class="col-6 pe-1">
                    <img src="../socialmedia/post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                  </a>
                  <a href="../socialmedia/post_images/<?= $post_img2 ?>" class="col-6 pe-1">
                    <img src="../socialmedia/post_images/<?= $post_img2 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                  </a>
                  <a href="../socialmedia/post_images/<?= $post_img3 ?>" class="col-6 pe-1">
                    <img src="../socialmedia/post_images/<?= $post_img3 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                  </a>
                <?php break;
                case 4: ?>
                  <a href="../socialmedia/post_images/<?= $post_img ?>" class="col-6 pe-1">
                    <img src="../socialmedia/post_images/<?= $post_img ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                  </a>
                  <a href="../socialmedia/post_images/<?= $post_img2 ?>" class="col-6 ps-1">
                    <img src="../socialmedia/post_images/<?= $post_img2 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                  </a>
                  <a href="../socialmedia/post_images/<?= $post_img3 ?>" class="col-6 pe-1">
                    <img src="../socialmedia/post_images/<?= $post_img3 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
                  </a>
                  <a href="../socialmedia/post_images/<?= $post_img4 ?>" class="col-6 ps-1">
                    <img src="../socialmedia/post_images/<?= $post_img4 ?>" style="width:100%;border-radius:5px;margin-top:15px;">
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
      <!-- Gönderi Beğeni Yorum -->
      <div class="row py-2 mt-2" id="likecomment_<?= $post_ID ?>">
        <div class="col-6 text-center text-dark border-end" id="like_<?= $post_ID ?>">
          <?php
          $is_liked = $db->getColumnData("SELECT * FROM postlike WHERE PostID = ? AND MemberID = ? ", array($post_ID, $memberid));
          $count_like = $db->getColumnData("SELECT COUNT(*) FROM postlike WHERE PostID = ?", array($post_ID));
          $count_comment = $db->getColumnData("SELECT COUNT(*) FROM postcomments WHERE PostID = ? AND CommentActive = ?", array($post_ID, 1));
          if (empty($is_liked)) { ?>
            <a class="text-decoration-none text-dark" onClick="Like('increaseLike','<?= $post_ID ?>')">
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

        <div class="col-6 text-center text-dark" id="comment_<?= $post_ID ?>">
          <a onClick="openComments(<?= $post_ID ?>)">
            <i class="far fa-comment-alt" style="cursor:pointer;"></i>
            <label style="cursor:pointer;" class="comment_label_<?= $post_ID ?>"> <?= $translates["comments"] ?> (<?= $count_comment ?>)</label>
          </a>
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
          <div class="row text-dark px-3 py-1 mt-md-2 each_comment_<?= $comment_ID ?>">
            <div class="col-2 p-0 pt-2 d-flex align-items-start justify-content-start">
              <a href="">
                <img src="images_profile/<?= $comment_profile_photo; ?>" class="rounded-circle" width="40" height="40">
              </a>
            </div>
            <div class="col-9 p-md-0">
              <div class="col-12">
                <a class="text-dark text-decoration-none" href="">
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
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
<?php } ?>