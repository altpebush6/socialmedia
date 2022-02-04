<?php

$matching = $db->getData("SELECT * FROM matching WHERE FirstMemberID = ? OR SecondMemberID = ?", array($memberid, $memberid));
if ($matching) {
    if ($matching->FirstMemberID == $memberid) {
        $matchedPersonID = $matching->SecondMemberID;
    } else {
        $matchedPersonID = $matching->FirstMemberID;
    }
    $matchedPerson = $db->getData("SELECT * FROM members WHERE MemberID = ?", array($matchedPersonID));
    $matchedPersonPP = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($matchedPersonID));
    $genderPerson = $matchedPerson->MemberGender;
    if (is_null($matchedPersonPP)) {
        if ($genderPerson == 'Male') {
            $matchedPersonPP = "profilemale.png";
        } else {
            $matchedPersonPP = "profilefemale.png";
        }
    }
?>
    <div class="container py-4 px-0 px-md-4">
        <div class="border col-md-10 offset-md-1 py-4 bg-light shadow" style="border-radius: 15px;">
            <div class="col-12 text-center fs-4">
                <div class="row">
                    <div class="col-12"><?= $translates["matchresult"] ?></div>
                    <div class="col-12 mb-2"><b><?= $matchedPerson->MemberNames ?></b></div>
                </div>
                <!-- <div class="row justify-content-center">
                    <div id="sayac">
                        <?php
                        $nextWeek = strtotime("7 February 2022");
                        $now = time();
                        $diff = $nextWeek - $now;

                        $day = intval($diff / 86400);
                        $diff = $diff % 86400;

                        $hour = intval($diff / 3600);
                        $diff = $diff % 3600;

                        $min = intval($diff / 60);
                        $diff = $diff % 60;

                        echo $day . " Gün : " . $hour . " Saat : " . $min . " Dakika : " . $diff . " Saniye";
                        ?>
                    </div>
                </div> -->
            </div>
            <div class="col-12 my-2 text-center">
                <a href="http://localhost/aybu/socialmedia/<?= $translates["profile"] ?>/<?= $memberid ?>" style="text-decoration:none;color:black">
                    <img src="images_profile/<?= $profile_photo ?>" width="70" height="70">
                </a>
                <span style="font-size:25px;font-weight:bold" class="mx-2">&#126;</span>
                <a href="http://localhost/aybu/socialmedia/<?= $translates["profile"] ?>/<?= $matchedPersonID ?>" style="text-decoration:none;color:black">
                    <img src="images_profile/<?= $matchedPersonPP ?>" width="70" height="70">
                </a>
            </div>
            <div class="col-12 px-5 text-center">
                <a href="http://localhost/aybu/socialmedia/<?= $translates["messages"] ?>/<?= $matchedPersonID ?>" class="btn btn-post mx-auto w-50 my-3"><?= $translates["starttalk"] ?></a>
            </div>
        </div>
    </div>
<?php } ?>