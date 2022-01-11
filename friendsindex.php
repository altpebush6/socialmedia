<?php

if ($part) {
    $profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($part));
    if (is_null($profile_photo)) {
        if ($gender == 'Male') {
            $profile_photo = "profilemale.png";
        } else {
            $profile_photo = "profilefemale.png";
        }
    }
    $user_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ?", array($part));
    $userid = $part;
} else {
    $userid = $memberid;
}

$friend_count = $db->getColumnData("SELECT COUNT(*) FROM friends WHERE (FirstMemberID = ? OR SecondMemberID = ?) AND FriendRequest = ?", array($userid, $userid, 1));
if ($friend_count) {
    $counts_friend = $friend_count;
} else {
    $counts_friend = '';
}

?>
<div class="container mt-4 friend-container shadow">
    <div class="row justify-content-center mt-3">
        <div class="col-4 text-center">
            <img src="images_profile/<?= $profile_photo ?>" class="rounded-circle" width="150" height="150">
        </div>
    </div>
    <div class="row justify-content-center my-2 d-none d-md-block">
        <div class="col-8 text-center mx-auto">
            <h2><?= $user_name . $translates["whosefriends"] . " " ?><span class="badge bg-primary friend_count" id="friend_count1"><?= $counts_friend ?></span></h2>
        </div>
    </div>
    <hr class="text-dark mb-3">
    <?php if (!$part) { ?>
        <div class="col-12 text-center d-md-none">
            <ul class="list-group friend_requests" id="friend_requests_sm">
                <?php
                $count_Requests = $db->getColumnData("SELECT COUNT(*) FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($userid, 0));
                if ($count_Requests) {
                    $counts = $count_Requests;
                } else {
                    $counts = '';
                }
                ?>
                <h3><?= $translates["friendrequests"] . " " ?><span class="badge bg-primary friend_request_count" id="friend_request_count_sm"><?= $counts ?></span></h3>
                <?php

                $friend_Requests = $db->getDatas("SELECT * FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($userid, 0));
                if ($friend_Requests) {
                    foreach ($friend_Requests as $items) {
                        $personID = $items->FirstMemberID;
                        $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
                        $personimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
                        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
                        if (is_null($personimg)) {
                            if ($gender == 'Male') {
                                $personimg = "profilefullmale.jpg";
                            } else {
                                $personimg = "profilefullfemale.jpg";
                            }
                        }
                ?>
                        <li class="list-group-item bg-transparent each_request_<?= $items->FriendID ?>" id="each_request_sm_<?= $items->FriendID ?>">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-2"><img src="images_profile/<?= $personimg ?>" class="rounded-circle" width="75" height="75"></div>
                                <div class="col-8 text-dark">
                                    <h4 class="person-h4"><?= $personNames ?></h4>
                                </div>
                                <div class="col-2">
                                    <i class="fas fa-times refuse-request" onClick="FriendAcceptment('refuse','<?= $personID ?>','<?= $items->FriendID ?>')"></i>
                                    <i class="fas fa-check accept-request" onClick="FriendAcceptment('accept','<?= $personID ?>','<?= $items->FriendID ?>')"></i>
                                </div>
                            </div>
                        </li>
                    <?php }
                } else { ?>
                    <li class="list-group-item bg-transparent" style="padding:5%;font-size:19px;color:white;"> <?= $translates["norequest"] ?> </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    <h2 class="d-md-none text-center my-4"><?= $user_name . $translates["whosefriends"] . " " ?><span class="badge bg-primary friend_count" id="friend_count2"><?= $counts_friend ?></span></h2>
    <div class="row">
        <div class="col-12 col-md-8 text-center">
            <ul class="list-group" id="friends_exist">
                <?php

                $friends = $db->getDatas("SELECT * FROM friends WHERE (FirstMemberID = ? OR SecondMemberID = ?) AND FriendRequest = ? ORDER BY FriendID DESC", array($userid, $userid, 1));
                if ($friends) {
                    foreach ($friends as $items) {
                        if ($items->FirstMemberID == $userid) {
                            $personID = $items->SecondMemberID;
                        } else {
                            $personID = $items->FirstMemberID;
                        }
                        $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
                        $personimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
                        $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
                        if (is_null($personimg)) {
                            if ($gender == 'Male') {
                                $personimg = "profilefullmale.jpg";
                            } else {
                                $personimg = "profilefullfemale.jpg";
                            }
                        }
                ?>
                        <li class="list-group-item bg-transparent mb-3 py-3">
                            <div class="row align-items-center justify-content-center">
                                <div class="col-2 text-center"><img src="images_profile/<?= $personimg ?>" class="rounded-circle" width="50" height="50"></div>
                                <div class="col-7 text-dark text-start">
                                    <h4><?= $personNames ?></h4>
                                </div>
                                <div class="col-3 p-0"><a class="btn btn-outline-theme" href="http://localhost/aybu/socialmedia/<?= $translates['profile'] ?>/<?= $personID ?>"><?= $translates["goprofile"] ?></a></div>
                            </div>
                        </li>
                    <?php }
                } else { ?>
                    <li class="each-friend-li text-dark" id="no_friends" style="padding:5%;font-size:19px;list-style: none;"><?= $translates["nofriends"] ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php if (!$part) { ?>
            <div class="col-4 d-none d-md-block">
                <ul class="list-group text-center friend_requests" id="friend_requests_lg">
                    <?php
                    $count_Requests = $db->getColumnData("SELECT COUNT(*) FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($userid, 0));
                    $counts = $count_Requests;
                    ?>
                    <h4 class="m-0"><?= $translates["friendrequests"] . " " ?><span class="badge bg-primary friend_request_count" id="friend_request_count_lg"><?= $counts ?></span></h4>
                    <?php

                    $friend_Requests = $db->getDatas("SELECT * FROM friends WHERE SecondMemberID = ? AND FriendRequest = ?", array($userid, 0));
                    if ($friend_Requests) {
                        foreach ($friend_Requests as $items) {
                            $personID = $items->FirstMemberID;
                            $personNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($personID));
                            $personimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($personID));
                            $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($personID));
                            if (is_null($personimg)) {
                                if ($gender == 'Male') {
                                    $personimg = "profilefullmale.jpg";
                                } else {
                                    $personimg = "profilefullfemale.jpg";
                                }
                            }
                    ?>
                            <li class="list-group-item bg-transparent each_request_<?= $items->FriendID ?>" style="border:none" id="each_request_lg_<?= $items->FriendID ?>">
                                <div class="row justify-content-center align-items-center">
                                    <div class="col-2"><img src="images_profile/<?= $personimg ?>" class="rounded-circle" width="50" height="50"></div>
                                    <div class="col-7">
                                        <h5 class="m-0 text-dark"><?= $personNames ?></h5>
                                    </div>
                                    <div class="col-3">
                                        <i class="fas fa-times refuse-request" onClick="FriendAcceptment('refuse','<?= $personID ?>','<?= $items->FriendID ?>')"></i>
                                        <i class="fas fa-check accept-request" onClick="FriendAcceptment('accept','<?= $personID ?>','<?= $items->FriendID ?>')"></i>
                                    </div>
                                </div>
                            </li>
                        <?php }
                    } else { ?>
                        <li class="list-group-item bg-transparent text-dark" style="padding:5%;font-size:19px;border:none"> <?= $translates["norequest"] ?> </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    </div>

</div>