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
<div class="row justify-content-center px-3">
  <div class="row align-items-center justify-content-between mb-1 py-2 px-0 messenger-top">
    <div class="col-8 mx-auto text-center">
      <img src="group_images/<?= $groupImage ?>" class="rounded-circle" width="60" height="60" id="chatpersonimg">
      <a class="text-light text-decoration-none fs-5" id="chatgroupname"><?= $group_name ?></a>
    </div>
  </div>
  <div class="row m-0 p-0 pb-2 messenger-middle d-flex flex-column justify-content-betweeen" id="messages">
    <ul class="list-group px-0 m-0" id="messages_container" style="height:49vh;overflow-y:auto">
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
          $imgmsg = '<img src="message_images/' . $item->MessageImg . '"  class="rounded-2" width="250">';
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
                                <span class="time-img text-light fs-6 m-2 p-1 align-self-start rounded-2 position-absolute" style="font-size: 13px !important;">
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
                                <img src="images_profile/' . $profile_photo . '"  class="rounded-circle" width="50" height="50">
                              </div>        
                              <div class="p-2 text-end msg-container" style="width:auto;max-width:250px;min-width:75px;">
                                <div class="me-2 del-msg"><i class="fas fa-trash position-absolute text-danger mt-2" onClick=\'DeleteMessage("deletemessage_group","' . $item->MessageID . '")\'></i></div>
                                  <div class="d-flex text-start flex-column row align-items-center bg-light text-dark rounded-3 d-flex flex-row flex-nowrap" style="height:100%;max-width:200px;">
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
                                <img src="images_profile/' . $personImage . '"  class="rounded-circle" width="50" height="50">
                              </div>      
                              <div class="col-10 d-flex justify-content-start p-0 text-start message-content-img">
                                <a class="w-33" href="message_images/' . $item->MessageImg . '">
                                <div class="position-absolute bg-dark text-light m-1 p-1 rounded-2" style="font-size:13px;">'.$personNames.'</div>
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
                                <img src="images_profile/' . $personImage . '"  class="rounded-circle" width="50" height="50">
                              </div>        
                              <div class="col-10 p-0" style="width:auto;max-width:250px;min-width:75px;">
                                <div class="row align-items-center bg-light text-dark rounded-3" style="height:100%;max-width:200px;">
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