<?php
$groupID = $edit;
$isreadmessage = $db->getColumnData("SELECT MessageHasRead FROM chatbox WHERE GroupID = ?", array($groupID));
$isreadmessage_array = explode(":", $isreadmessage);
if (!in_array($memberid, $isreadmessage_array)) {
  if ($isreadmessage == 0) {
    $readMessage = $db->Update("UPDATE chatbox SET MessageHasRead = ? WHERE GroupID = ?", array(($memberid . ":"), $groupID));
  } else {
    $newvalue = $isreadmessage . $memberid . ":";
    $readMessage = $db->Update("UPDATE chatbox SET MessageHasRead = ? WHERE GroupID = ?", array($newvalue, $groupID));
  }
}
$group_name = $db->getColumnData("SELECT GroupName FROM all_groups WHERE GroupID = ?", array($groupID));
$groupImage = $db->getColumnData("SELECT GroupImage FROM all_groups WHERE GroupID = ?", array($groupID));
if (is_null($groupImage)) {
  $groupImage = "noneimage.png";
}
?>
<div class="row justify-content-center ps-3 pe-1">
  <div class="row align-items-center justify-content-between py-2 px-0 messenger-top shadow" data-bs-toggle="modal" data-bs-target="#groupinfo">
    <div class="col-8 mx-auto text-center">
      <img src="group_images/<?= $groupImage ?>" class="rounded-circle" width="60" height="60" id="chatpersonimg">
      <a class="text-dark text-decoration-none fs-5" id="chatgroupname"><?= $group_name ? $group_name : $translates["anonymousgrp"] ?></a>
    </div>
    <div class="col-8 mx-auto mt-1 pt-2 border-top text-center text-dark">
      <?php
      $groupMembers = $db->getColumnData("SELECT GroupMembers FROM all_groups WHERE GroupID = ?", array($groupID));
      $groupMembers = explode(":", $groupMembers);
      $groupMembersNum = count($groupMembers);
      unset($groupMembers[$groupMembersNum - 1]);
      foreach ($groupMembers as $eachMember) {
        $g_memberName = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($eachMember));
        $g_memberLastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = ?", array($eachMember));
        if ($g_memberName) {
          echo $g_memberName . " " . $g_memberLastname[0] . ", ";
        }
      }
      ?>
    </div>
  </div>
  <div class="row m-0 p-0 pb-2 messenger-middle d-flex flex-column justify-content-betweeen" id="messages">
    <ul class="list-group px-0 m-0" id="messages_container" style="height:43vh;overflow-y:auto">
      <?php
      $texts = $db->getDatas("SELECT * FROM messages_group WHERE MessageStatus = ? AND GroupID = ? ORDER BY MessageAddTime", array(1, $groupID));
      foreach ($texts as $item) {
        $MessageFrom = $item->MessageFromID;
        $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($MessageFrom));
        $personImage = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($MessageFrom));
        $persongender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($MessageFrom));
        if (is_null($personImage)) {
          if ($persongender == 'Erkek') {
            $personImage  = "profilemale.png";
          } else {
            $personImage = "profilefemale.png";
          }
        }
        if ($item->MessageImg) {
          $imgmsg = '<img src="message_images/' . $item->MessageImg . '"  class="rounded-2" style="width:250px;min-height:20vh;">';
        }
        if ($MessageFrom == $memberid) {
          if ($item->MessageImg) {
            echo '<li class="list-group-item bg-transparent my-2 p-4 py-1" style="border:none;" id="each_message_' . $item->MessageID . '" lastid="' . $item->MessageID . '">
                            <div class="row d-flex flex-row-reverse">   
                              <div class="col-2 col-xl-1 p-0 text-center">
                                <img src="images_profile/' . $profile_photo . '"  class="rounded-circle" width="50" height="50">
                              </div>      
                              <div class="col-10 d-flex justify-content-end p-0 text-end message-content-img">
                                <a class="w-33" href="message_images/' . $item->MessageImg . '">' . $imgmsg . '</a>
                                <span class="time-img text-dark fs-6 m-2 p-1 align-self-start rounded-2 position-absolute" style="font-size: 13px !important;">
                                <i class="fas fa-trash text-danger fs-6 del-img" onClick=\'DeleteMessage("deletemessage_group","' . $item->MessageID . '")\'></i>
                                ' . messageTime($item->MessageAddTime) . '
                                </span>
                              </div>
                            </div>
                          </li>';
          } else {
            echo '<li class="list-group-item bg-transparent my-2 p-4 py-1" style="border:none;" id="each_message_' . $item->MessageID . '" lastid="' . $item->MessageID . '">
                            <div class="row d-flex flex-row-reverse"> 
                              <div class="col-2 col-xl-1 ms-md-1 ms-xl-2 p-0 text-center d-flex justify-content-center align-items-center">
                                <img src="images_profile/' . $profile_photo . '"  class="rounded-circle shadow-lg" width="50" height="50">
                              </div>        
                              <div class="p-2 text-end msg-container" style="width:auto;max-width:250px;min-width:75px;">
                                <div class="me-2 del-msg"><i class="fas fa-trash position-absolute text-danger mt-2" onClick=\'DeleteMessage("deletemessage_group","' . $item->MessageID . '")\'></i></div>
                                  <div class="d-flex text-start flex-column shadow row align-items-center bg-light text-dark rounded-3 d-flex flex-row flex-nowrap" style="height:100%;max-width:200px;">
                                    <div class="p-0 w-100" style="width:auto;max-width:200px;">
                                      <p class="m-0 py-1 px-2 fs-6 text-break">' . $item->MessageText . '</p>
                                    </div>
                                    <div class="bg-light text-dark text-end px-2 rounded-3" style="border-top-left-radius:0px !important;border-top-right-radius:0px !important;font-size:12px">
                                      ' . messageTime($item->MessageAddTime) . '
                                    </div>  
                                  </div> 
                                </div>
                            </div>
                          </li>';
          }
        } else {
          if ($item->MessageImg) {
            echo '<li class="list-group-item bg-transparent my-2 p-4 py-1" style="border:none;" id="each_message_' . $item->MessageID . '" lastid="' . $item->MessageID . '">
                            <div class="row">
                              <div class="col-2 p-0 col-lg-1 text-center me-lg-3">
                              <a href="http://localhost/aybu/socialmedia/' . $translates["profile"] . '/' . $MessageFrom . '">
                                <img src="images_profile/' . $getprofileimg . '" class="rounded-circle shadow-lg" width="60" height="60">
                              </a>
                              </div>      
                              <div class="col-10 d-flex justify-content-start p-0 text-start message-content-img">
                                <a class="w-33" href="message_images/' . $item->MessageImg . '">
                                <div class="position-absolute bg-dark text-light m-1 p-1 rounded-2" style="font-size:13px;">' . $personNames . '</div>
                                ' . $imgmsg . '
                                </a>
                                <span class="time-img text-light fs-6 m-2 p-1 align-self-end rounded-2 position-absolute" style="font-size: 13px !important;">
                                ' . messageTime($item->MessageAddTime) . '
                                </span>
                              </div>
                            </div>
                          </li>';
          } else {
            echo '<li class="list-group-item bg-transparent my-2 p-4 py-1" style="border:none;" id="each_message_' . $item->MessageID . '" lastid="' . $item->MessageID . '">
                            <div class="row">
                              <div class="col-2 col-lg-1 text-center p-0 me-2 me-md-3 me-lg-4">
                              <a href="http://localhost/aybu/socialmedia/' . $translates["profile"] . '/' . $MessageFrom . '">
                                <img src="images_profile/' . $getprofileimg . '" class="rounded-circle shadow-lg" width="60" height="60">
                              </a>
                              </div>        
                              <div class="col-10 p-0" style="width:auto;max-width:250px;min-width:75px;">
                                <div class="row align-items-center shadow bg-light text-dark rounded-3" style="height:100%;max-width:200px;">
                                  <div class="p-0 w-100" style="width:auto;">
                                    <p class="m-0 px-2 text-break bg-dark text-light border-bottom" style="font-size:13px;padding-bottom:1px;padding-top:1px;border-top-left-radius:0.3rem;border-top-right-radius:0.3rem;">' . $personNames . '</p>
                                    <p class="m-0 py-1 px-2 fs-6 text-break">' . $item->MessageText . '</p>
                                  </div>
                                  <div class="bg-light text-dark text-end px-2 rounded-3" style="border-top-left-radius:0px !important;border-top-right-radius:0px !important;font-size:12px">
                                    ' . messageTime($item->MessageAddTime) . '
                                  </div>
                                </div>
                              </div>
                            </div>   
                          </li>';
          }
        }
      }
      ?>
    </ul>
  </div>
  <div class="row my-3 align-items-center justify-content-center">
    <div class="col-2 text-end">
      <form id="form_send_img" method="post" enctype="multipart/form-data">
        <input type="file" name="img_message" id="img_message" style="display:none;" accept=image/x-png,image/gif,image/jpeg>
        <label for="img_message" class="rounded-3 mx-1 btn btn-sm btn-outline-dark"><i class="fas fa-image"></i></label>
      </form>
    </div>
    <div class="col-10">
      <form id="form_send_message" method="post">
        <div class="row">
          <div class="col-10 text-center p-0">
            <input class="form-control form-control-sm message-input" autocomplete="off" type="text" maxlength="200" name="messageText" id="messageText" placeholder="<?= $translates["sendamessage"] ?>">
          </div>
          <div class="col-2 text-start">
            <?php if (!$groupid) {
              $groupid = null;
            } ?>
            <button type="button" name="sendMessageBtn" class="btn btn-outline-dark btn-sm rounded-3" id="sendMessageBtn" onClick="SendMessage('sendmessage_group','<?= $memberid ?>','<?= $groupID ?>')">
              <span class="spinner" id="spinnersendmessage"></span>
              <i class="far fa-paper-plane" id="papericon"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- MODAL -->
<div class="modal fade" id="groupinfo">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="row w-100">
          <div class="col-12 mb-1">
            <label class="text-muted" style="font-size: 14px;"><?= $translates["groupname"] ?></label>
          </div>
          <div class="col-10">
            <span class="modal-title mx-auto" id="groupName"><?= $group_name ? $group_name : $translates["anonymousgrp"] ?></span>
            <input type="text" class="form-control d-none" id="groupNameInput" placeholder="<?= $translates["entergroupname"] ?>">
          </div>
          <div class="col-2 m-0 p-0 text-end">
            <i class="fas fa-marker editgroupName"></i>
            <button type="button" class="btn btn-outline-success d-none" id="nameBtn" groupid="<?= $groupID ?>"><?= $translates["save"] ?> <span class="spinner" id="spinnerName"></span></button>
          </div>
        </div>
      </div>
      <?php $group_exp = $db->getColumnData("SELECT GroupExplanation FROM all_groups WHERE GroupID = ?", array($groupID)); ?>
      <div class="modal-header">
        <div class="row w-100">
          <div class="col-12 mb-1">
            <label class="text-muted" style="font-size: 14px;"><?= $translates["groupexp"] ?></label>
          </div>
          <div class="col-10">
            <span class="modal-title mx-auto" id="groupExp"><?= ($group_exp) ? $group_exp : $translates["nogroupexp"] ?></span>
            <input type="text" class="form-control d-none" id="expInput" placeholder="<?= $translates["entergroupexp"] ?>">
          </div>
          <div class="col-2 m-0 p-0 text-end">
            <i class="fas fa-marker editExp"></i>
            <button type="button" class="btn btn-outline-success d-none" id="expBtn" groupid="<?= $groupID ?>"><?= $translates["save"] ?> <span class="spinner" id="spinnerExp"></span></button>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="row mb-2">
          <div class="col-12" id="groupMemberNum"><?= ($groupMembersNum - 1) . " " . $translates["people"] ?></div>
        </div>
        <div class="mb-3 mt-2 d-flex flex-row justify-content-center align-items-center">
          <i class="fas fa-user-plus btn btn-success d-flex justify-content-center align-items-center me-2" data-bs-toggle="modal" data-bs-target="#addMember" style="width: 35px;height: 35px;border-radius:25px;font-size:13px"></i>
          <label class="text-success fs-5" style="cursor: pointer;font-weight: 600;" data-bs-toggle="modal" data-bs-target="#addMember"><?= $translates["addmember"] ?></label>
        </div>
        <div class="row mb-2">
          <div class="input-group">
            <input type="text" class="form-control" groupid="<?= $groupID ?>" placeholder="<?= $translates["entername"] ?>" id="groupMemberName" aria-describedby="basic-addon1">
            <span class="input-group-text" id="searchMembers"><i class="fas fa-search"></i></span>
          </div>
        </div>
        <div class="row px-2 d-block" style="height: 35vh;overflow-x:hidden;overflow-y:auto;" id="groupMembers">
          <?php foreach ($groupMembers as $eachMember) {
            $isadmin = 0;
            $memberNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($eachMember));
            $memberImg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($eachMember));
            if (is_null($memberImg)) {
              if ($gender == 'Erkek') {
                $memberImg = "profilemale.png";
              } else {
                $memberImg = "profilefemale.png";
              }
            }
          ?>
            <div class="col-12 border py-2" id="groupMember_<?= $eachMember ?>">
              <div class="row">
                <div class="col-2">
                  <img src="images_profile/<?= $memberImg ?>" style="width:50px;height:50px;" class="rounded-circle border">
                </div>
                <div class="col-5 m-0 p-0 d-flex justify-content-start align-items-center fs-5">
                  <span><?= $memberNames ?></span>
                </div>
                <div class="col-5 p-0 m-0 pe-3 d-flex align-items-center justify-content-end" id="memberOperations_<?= $eachMember ?>">
                  <?php
                  $admins = $db->getColumnData("SELECT GroupAdmins FROM all_groups WHERE GroupID = ?", array($groupID));
                  $admins = explode(":", $admins);
                  foreach ($admins as $admin) {
                    if ($admin == $eachMember) {
                      $isadmin = 1;
                    }
                  }
                  if ($isadmin) { ?>
                    <span class="p-1 rounded-1" style="color:green;border:1px solid green;font-size:12px" id="admin_<?= $eachMember ?>"><?= $translates["gradmin"] ?></span>
                    <?php if ($eachMember != $memberid) { ?>
                      <button type="button" class="btn btn-sm ms-2 btn-outline-warning demoteMember" id="division_<?= $eachMember ?>" groupid="<?= $groupID ?>" memberid="<?= $eachMember ?>"><i class="fas fa-angle-double-down px-1"></i></button>
                    <?php }
                  } else {
                    if ($eachMember != $memberid) {  ?>
                      <button type="button" class="btn btn-sm ms-2 btn-outline-success promoteMember" id="division_<?= $eachMember ?>" groupid="<?= $groupID ?>" memberid="<?= $eachMember ?>"><i class="fas fa-angle-double-up px-1"></i></button>
                    <?php }
                  }
                  foreach ($admins as $admin) {
                    if ($admin == $memberid) {
                      $amiadmin = 1;
                    }
                  }
                  if ($amiadmin && $eachMember != $memberid) { ?>
                    <button type="button" class="btn btn-sm ms-2 btn-outline-danger removeMember" groupid="<?= $groupID ?>" memberid="<?= $eachMember ?>"><i class="fas fa-user-slash"></i></button>
                  <?php } ?>

                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="modal-footer text-end">
        <?php
        if ($amiadmin) { ?>
          <button type="submit" id="delGroup" class="btn btn-dark w-33"><?= $translates["delgroup"] ?> <span class="spinner" id="spinnerdelGroup"></span></button>
        <?php } ?>
        <button type="submit" id="leaveGroup" class="btn btn-outline-danger w-33"><?= $translates["leavegroup"] ?> <span class="spinner" id="spinnerleaveGroup"></span></button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="addMember" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-light">
      <div class="modal-header">
        <h5 class="modal-title"><?= $translates["addmember"] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-2">
          <div class="input-group">
            <input type="text" class="form-control" groupid="<?= $groupID ?>" placeholder="<?= $translates["entername"] ?>" id="allMembersName">
            <span class="input-group-text" id="searchMembers"><i class="fas fa-search"></i></span>
          </div>
          <div class="row m-0 p-0 mt-2 d-block" style="height: 60vh;overflow-x:hidden;overflow-y:auto;" id="allMembers">
            <?php
            $allmembers = $db->getDatas("SELECT * FROM members WHERE MemberConfirm = ? ORDER BY MemberName", array(1));
            foreach ($allmembers as $eachMember) {
              $isMember = 0;
              $eachmemberID = $eachMember->MemberID;
              $allGroupMembers = $db->getColumnData("SELECT GroupMembers FROM all_groups WHERE GroupID = ?", array($groupID));
              $allGroupMembers = explode(":", $allGroupMembers);
              foreach ($allGroupMembers as $groupMember) {
                if ($eachmemberID == $groupMember) {
                  $isMember = 1;
                }
              }
              if (!$isMember) {
                $memberNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($eachmemberID));
                $memberImg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($eachmemberID));
                if (is_null($memberImg)) {
                  if ($gender == 'Erkek') {
                    $memberImg = "profilemale.png";
                  } else {
                    $memberImg = "profilefemale.png";
                  }
                }
            ?>
                <div class="col-11 mx-auto border m-0 py-2" id="allMembers_<?= $eachmemberID ?>">
                  <div class="row">
                    <div class="col-2">
                      <img src="images_profile/<?= $memberImg ?>" style="width:50px;height:50px;" class="rounded-circle border">
                    </div>
                    <div class="col-7 m-0 p-0 d-flex justify-content-start align-items-center fs-5">
                      <span><?= $memberNames ?></span>
                    </div>
                    <div class="col-3 d-flex justify-content-end align-items-center">
                      <div class="border d-flex justify-content-center align-items-center text-success addMemberIcon" id="operation_<?= $eachmemberID ?>" memberid="<?= $eachmemberID ?>" groupid="<?= $groupID ?>">
                        <i class="fas fa-plus" id="icon_<?= $eachmemberID ?>"></i>
                      </div>
                    </div>
                  </div>
                </div>
            <?php }
            } ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="reloadPage"><?= $translates["reload"] ?></button>
      </div>
    </div>
  </div>
</div>
<script>
  $(function() {
    $("#reloadPage").on("click", function() {
      location.reload();
    });
  });
</script>