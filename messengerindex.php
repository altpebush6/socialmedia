<div class="container messanger-container mt-md-5">
  <div class="row m-0 p-0" style="min-height: 65vh;">
    <div class="col-12 col-md-5 col-xl-3">
      <div class="row justify-content-center py-3">
        <div class="col-12 text-end">
          <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#creategroup"><?= $translates["creategroup"] ?></button>
        </div>
        <div class="col-12 text-center">
          <img src="images_profile/<?= $profile_photo; ?>" class="rounded-circle" width="100" height="100">
        </div>
        <div class="col-12 my-2 text-center">
          <h3 style="font-family: 'IBM Plex Sans Arabic', sans-serif;"><?= $user_name . " " . $user_lastname; ?></h3>
        </div>
        <hr>
        <div class="row">
          <?php

          $chatpersons = $db->getDatas("SELECT * FROM chatbox
                                        WHERE MessageStatus = 1 AND (MessageFromID = $memberid OR MessageToID = $memberid OR GroupMembers LIKE '%$memberid%')
                                        ORDER BY LastTime DESC");
          ?>
          <div class="col-12 text-center mt-0 mb-2">
            <input class="form-control w-100 mx-auto srchfriend" autocomplete="off" type="text" id="srchformsg" name="srchformsg" placeholder="<?= $translates["searchformessage"] ?>">
          </div>
        </div>
        <div id="contactmain" style="max-height:30vh;overflow:auto">
          <?php
          foreach ($chatpersons as $info) {
            $groupID = $info->GroupID;
            if ($groupID) {
              $groupInfos = $db->getData("SELECT * FROM all_groups WHERE GroupID = ?", array($groupID));
              if (!$info->MessageFromID) {
                $groupCreatorID = $groupInfos->GroupCreator;
                $CreatorName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($groupCreatorID));
                $groupMessage = $CreatorName . " " . $translates["personcreatedgroup"];
              } else {
                $lastmessage = $db->getData("SELECT * FROM messages_group WHERE GroupID = ? AND MessageStatus = ? ORDER BY MessageAddTime DESC", array($groupID, 1));
                $whosemessage = $lastmessage->MessageFromID;
                if ($whosemessage == $memberid) {
                  $fromwho = $translates["you"];
                } else {
                  $fromwho = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($whosemessage));
                }
                if ($lastmessage->MessageImg) {
                  $groupMessage = $fromwho . ": " . '<i class="fas fa-camera"></i> ' . $translates["photo"];
                } else {
                  $groupMessage = $fromwho . ": " . $lastmessage->MessageText;
                }
                $messageHasRead = $info->MessageHasRead;
                $messageHasRead = explode(":", $messageHasRead);
                if (!in_array($memberid, $messageHasRead) && $groupID != $edit && $memberid != $whosemessage) {
                  $styleperson = "style='opacity:1'";
                } else {
                  $styleperson =  "style='opacity:0.5'";
                }
              }

              $groupID = $groupInfos->GroupID;
              $groupimg = $groupInfos->GroupImage;
              if (is_null($groupimg)) {
                $groupimg = "noneimage.png";
              }
              $groupName = $groupInfos->GroupName;

              $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages_group WHERE GroupID = ? AND MessageStatus = ? ORDER BY MessageAddTime DESC", array($groupID, 1));

          ?>

              <a class="text-light text-decoration-none" id="person_<?= $groupID ?>" href="http://localhost/aybu/socialmedia/<?= $translates['messages'] ?>/<?= $translates["group"] ?>/<?= $groupID ?>" <?php echo ($groupID == $part ? "style='background:rgba(255, 255, 255, 0.2)'" : "style=''") ?>>
                <div class="row my-2 justify-content-center align-items-center">
                  <div class="col-2 text-center">
                    <img src="group_images/<?= $groupimg ?>" class="rounded-circle" width="60" height="60">
                  </div>
                  <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                    <div class="row fs-5">
                      <div class="col-12 p-0 messenger-names"><?= $groupName ?></div>
                    </div>
                    <div class="row">
                      <div class="col-9 p-0 text-start person-content" id="content_<?= $groupID ?>" <?= $styleperson ?>>
                        <?= $groupMessage ?>
                      </div>
                      <?php
                      if ($info->MessageFromID) { ?>
                        <div class="col-3 pe-1 text-end">
                          <small><?= messageTime($messagetime) ?></small>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </a>

            <?php } else {
              if ($info->MessageFromID == $memberid) {
                $personID = $info->MessageToID;
              } else {
                $personID = $info->MessageFromID;
              }
              $getprofileimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
              $ChatPersonName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($personID));
              $ChatPersonLastName  = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = ?", array($personID));
              if (is_null($getprofileimg)) {
                if ($gender == 'Erkek') {
                  $getprofileimg = "profilemale.png";
                } else {
                  $getprofileimg = "profilefemale.png";
                }
              }

              $name_lastname = $ChatPersonName . " " . $ChatPersonLastName;

              $messageID = $db->GetColumnData("SELECT MessageID FROM messages
                                              WHERE MessageStatus = 1 AND ((MessageFromID = $memberid AND MessageToID = $personID) OR (MessageFromID = $personID AND MessageToID = $memberid))
                                              ORDER BY MessageAddTime DESC");

              $messageText = $db->getColumnData("SELECT MessageText FROM messages WHERE MessageID = ?", array($messageID));
              $messageImg = $db->getColumnData("SELECT MessageImg FROM messages WHERE MessageID = ?", array($messageID));

              $whosemessage =  $db->GetColumnData("SELECT MessageFromID FROM messages WHERE MessageID = ?", array($messageID));

              $messagetime = $db->GetColumnData("SELECT MessageAddTime FROM messages
                                              WHERE MessageID = ? AND MessageStatus = ?", array($messageID, 1));

              if ($whosemessage != $memberid) {
                $messageHasRead = $info->MessageHasRead;
              } else {
                $messageHasRead = 1;
              }

              if ($whosemessage == $memberid) {
                $messageHasSeen = $db->getColumnData("SELECT MessageHasSeen FROM messages WHERE MessageID = ?", array($messageID));
                if ($messageHasSeen == 1) {
                  $tic = ' <i class="fas fa-check-double text-primary" style="font-size:13px;"></i>';
                } else {
                  $tic = ' <i class="fas fa-check" style="font-size:13px;"></i>';
                }
                $fromwho = $translates["you"];
              } else {
                $fromwho = $ChatPersonName;
                $tic = '';
              }


              if (($messageHasRead == 0) && ($personID != $part)) {
                $styleperson = "style='opacity:1'";
              } else {
                $styleperson =  "style='opacity:0.5'";
              }

              $time = $db->getColumnData("SELECT MemberTime FROM members WHERE MemberID = ?", array($personID));
              $now_time = date("Y-m-d H:i:s");
              $strt = strtotime($time);
              $fnsh = strtotime($now_time);
              $diff = abs($fnsh - $strt);
              if ($diff < 10) {
                $result = "style='color:green'";
              } else {
                $result = "style='color:rgb(204, 1, 1)'";
              }
              if ($messageImg) {
                $messageText = '<i class="fas fa-camera"></i> ' . $translates["photo"];
              }

              $resultcontent = $fromwho . ": " . $messageText . $tic;
            ?>
              <a class="text-light text-decoration-none" id="person_<?= $personID ?>" href="http://localhost/aybu/socialmedia/<?= $translates['messages'] ?>/<?= $personID ?>" <?php echo ($personID == $part ? "style='background:rgba(255, 255, 255, 0.2)'" : "style=''") ?>>
                <div class="row my-2 justify-content-center align-items-center">
                  <div class="col-2 text-center">
                    <img src="images_profile/<?= $getprofileimg ?>" class="rounded-circle" width="60" height="60">
                  </div>
                  <div class="col-10 px-3 ps-4 ps-md-5 ps-lg-4 ps-xl-5">
                    <div class="row fs-5">
                      <div class="col-10 p-0 messenger-names"><?= $name_lastname ?></div>
                      <div class="col-2"><i class="fas fa-circle offline" id="chatperson_<?= $personID ?>" <?= $result ?>></i></div>
                    </div>
                    <div class="row">
                      <div class="col-8 p-0 text-start person-content" id="content_<?= $personID ?>" <?= $styleperson ?>>
                        <?= $resultcontent ?>
                      </div>
                      <div class="col-4 m-0 p-0 pe-1 text-end"><small><?= messageTime($messagetime) ?></small></div>
                    </div>
                  </div>
                </div>
              </a>
          <?php }
          } ?>
        </div>
      </div>
    </div>
    <hr class="d-md-none mb-0">
    <div class="col-12 col-md-7 col-xl-9 col p-0 mb-4 mb-md-0 border-md-start">

      <?php if (!$part) { ?>
        <div class="row d-flex flex-column justify-content-center align-items-center py-4" style="height: 100%;">

          <div class="col-12 text-center">
            <img src="images_profile/<?= $profile_photo ?>" class="rounded-circle" width="100" height="100">
          </div>
          <div class="col-12 text-center">
            <h2 class="mt-2 mb-4"><?= $user_name . ', ' . $translates["sayhifriend"] ?></h2>
          </div>

          <div class="col-10 col-md-8 border text-center" style="max-height:35vh;overflow-y:auto;box-shadow: 1px 1px 5px 1px black;">
            <?php

            $members = $db->getDatas("SELECT * FROM members ORDER BY MemberTime DESC");
            $personFriends = $db->getDatas("SELECT * FROM friends WHERE (FirstMemberID = ? OR SecondMemberID = ?) AND FriendRequest = ?", array($memberid, $memberid, 1));
            if (empty($personFriends)) {
              $nofriend = '<p class="no-friend text-light fs-4 mt-3" style=\'font-family: "Roboto Slab", serif;\'>' . $translates["nofriend"] . '</p>';
            }
            echo $nofriend;

            foreach ($members as $person) {
              $FriendID = $person->MemberID;
              $isFriend = $db->getData("SELECT * FROM friends WHERE ((FirstMemberID = ? AND SecondMemberID = ?) OR (SecondMemberID = ? AND FirstMemberID = ?)) AND FriendRequest = ?", array($FriendID, $memberid, $FriendID, $memberid, 1));
              if ($isFriend) {
                $friendimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($FriendID));
                $friendnames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($FriendID));
                if (is_null($friendimg)) {
                  if ($gender == 'Erkek') {
                    $friendimg = "profilemale.png";
                  } else {
                    $friendimg = "profilefemale.png";
                  }
                }

                $time = $db->getColumnData("SELECT MemberTime FROM members WHERE MemberID = ?", array($FriendID));
                $now_time = date("Y-m-d H:i:s");
                $strt = strtotime($time);
                $fnsh = strtotime($now_time);
                $diff = abs($fnsh - $strt);
                if ($diff < 10) {
                  $result = "style='color:green'";
                } else {
                  $result = "style='color:rgb(204, 1, 1)'";
                }
            ?>
                <a href="http://localhost/aybu/socialmedia/<?= $translates['messages'] ?>/<?= $FriendID ?>" class="text-decoration-none text-light py-2 d-flex flex-row justify-content-between align-items-center border-bottom persons_sayhi">
                  <div class="d-flex flex-row align-items-center">
                    <img src="images_profile/<?= $friendimg ?>" class="rounded-circle mx-2" width="50" height="50">
                    <h5 class="mx-2"><?= $friendnames ?></h5>
                  </div>
                  <i class="fas fa-circle offline mx-2" id="chatfriend_<?= $FriendID ?>" <?= $result ?>></i>
                </a>
            <?php }
            } ?>
          </div>
        </div>
      <?php } else {
        if ($part == $translates["group"]) {
          require_once "groupmessages.php";
        } else {
          require_once "personalmessages.php";
        } ?>
      <?php } ?>
    </div>
  </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="creategroup">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createGroupLabel"><?= $translates["creategroup"] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row" id="added_persons">
          <div class="col-12 py-2">
            <div class="owl-carousel owl-theme d-flex justify-content-start" id="containermembers"></div>
          </div>
        </div>
        <form method="post" id="form_createGroup" autocomplete="off">
          <div class="row mb-3">
            <div class="col-12">
              <label for="name" class="form-label text-muted"><?= $translates["groupname"] . "*" ?></label>
              <input class="form-control" type="text" name="groupname" id="groupname" maxlength="40" placeholder="<?= $translates["entergroupname"] ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-12">
              <label for="name" class="form-label text-muted"><?= $translates["groupmembers"] . "*" ?></label>
              <input class="form-select" placeholder="<?= $translates["selectgroupmembers"] ?>" name="groupmembers" id="groupmembers" style="cursor:pointer" alladded="">
              <div id="allFriends" style="display: none;max-height:20vh;overflow-y:auto">
                <ul class="list-group" id="FriendsList">
                  <?php
                  $allmembers = $db->getDatas("SELECT * FROM members WHERE MemberConfirm = ? ORDER BY MemberNames ASC", array(1));
                  foreach ($allmembers as $eachmember) {
                    $friendID = $eachmember->MemberID;
                    $isfriend = $db->getData("SELECT * FROM friends WHERE (FirstMemberID = ? AND SecondMemberID = ?) OR (FirstMemberID = ? AND SecondMemberID = ?) AND FriendRequest = ?", array($friendID, $memberid, $memberid, $friendID, 1));
                    if ($isfriend) {
                      $friendNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($friendID));
                      $friendIMG = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($friendID));
                      $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($friendID));

                      if (is_null($friendIMG)) {
                        if ($gender == 'Erkek') {
                          $friendIMG = "profilemale.png";
                        } else {
                          $friendIMG = "profilefemale.png";
                        }
                      }


                  ?>
                      <li class="list-group-item each-friend" id="<?= $friendID ?>" style="cursor:pointer">
                        <img src="images_profile/<?= $friendIMG ?>" class="rounded-circle" style="width:40px;height:40px;">
                        <span><?= $friendNames ?></span>
                      </li>
                  <?php }
                  } ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-12">
              <label for="groupimg" class="form-label text-muted"><?= $translates["groupimg"] ?></label>
              <input class="form-control" id="groupimg" name="groupimg" type="file">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-12">
              <label for="groupexp" class="form-label text-muted"><?= $translates["groupexp"] ?></label>
              <input class="form-control" id="groupexp" maxlength="100" name="groupexp" type="text" placeholder="<?= $translates["entergroupexp"] ?>">
            </div>
          </div>
          <p id="resultgroup"></p>
      </div>
      <div class="modal-footer">
        <button type="submit" id="addgroup_btn" class="btn btn-primary w-100"><?= $translates["addgroup"] ?> <span class="spinner" id="spinneraddgroup"></span></button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function() {
    $('.owl-carousel').owlCarousel({
      loop: false,
      rewind: false,
      center: false,
      autoWidth: true,
      autoplay: 5000,
    })
  });

  baguetteBox.run('.message-content-img');

  function openEmojis() {
    $("#attachments").html("");
    const AllEmojis = [];
    for (let i = 13; i <= 67; i++) {
      AllEmojis.push("<label class='my-2 mx-1 fs-5' style='cursor:pointer' onClick=\"putEmoji('&#1285" + i + "')\">&#1285" + i + "</label>");
    }
    for (let j = 0; j <= 18; j++) {
      $("#attachments").append(AllEmojis[j]);
    }
  }

  function putEmoji(Emoji) {
    var firstVal = $("#messageText").val();
    $("#messageText").val(firstVal + Emoji);
  }

  function rotateattc() {
    if (document.getElementById("attachmenticon").style.transform == "rotate(45deg)") {
      document.getElementById("attachmenticon").style.transform = "rotate(0deg)";
      document.getElementById("attachments_container").style.visibility = "hidden";
      document.getElementById("attachments_container").style.opacity = "0";
    } else {
      document.getElementById("attachmenticon").style.transform = "rotate(45deg)";
      document.getElementById("attachments_container").style.visibility = "visible";
      document.getElementById("attachments_container").style.opacity = "1";
    }
  }
</script>