<?php $clubPresidentID = $db->getColumnData("SELECT ClubPresidentID FROM clubs WHERE ClubID = ?", array($part));
$clubRequests = $db->getDatas("SELECT * FROM clubmembers WHERE ClubID = ? AND Activeness = ?", array($part, 0));
if (($memberid == $clubPresidentID) and ($clubRequests)) { ?>
    <div class="row my-4" id="membership_requests">
        <h3 class="text-center text-dark mb-4">Kulüp üye istekleri</h3>
        <div class="col-6 mx-auto">
            <ul class="list-group p-0" style="max-height:28vh;overflow-y:auto">
                <?php
                foreach ($clubRequests as $memberinfo) {
                    $memberNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($memberinfo->MemberID));
                    $memberimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberinfo->MemberID));
                    $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($memberinfo->MemberID));
                    if (is_null($memberimg)) {
                        if ($gender == 'Erkek') {
                            $memberimg = "profilemale.png";
                        } else {
                            $memberimg = "profilefemale.png";
                        }
                    } ?>
                    <li class="list-group-item bg-transparent border-bottom" style="border:none;" id="request_<?= $memberinfo->MembershipID ?>">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-2"><img src="images_profile/<?= $memberimg ?>" class="rounded-circle" width="50" height="50"></div>
                            <div class="col-7">
                                <h5 class="m-0 text-dark"><?= $memberNames ?></h5>
                            </div>
                            <div class="col-3 text-end">
                                <i class="fas fa-times me-2 refuse-request" membershipid="<?= $memberinfo->MembershipID ?>"></i>
                                <i class="fas fa-check accept-request" membershipid="<?= $memberinfo->MembershipID ?>"></i>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
<?php } ?>
<!-- YÖNETİM -->
<div class="row">
    <div class="col-4 mx-auto border-bottom text-dark">
        <h3 class="text-center"><?= $translates["management"] ?></h3>
    </div>
</div>
<div class="row mt-2 justify-content-center">
    <?php
    $managementmembers = $db->getDatas("SELECT * FROM clubmembers WHERE ClubID = ? AND (MemberPosition = ? OR MemberPosition = ?) AND Activeness = ?", array($part, "Management", "President", 1));
    if (count($managementmembers) > 5) { ?>
        <div class="col-1 d-flex justify-content-center align-items-center">
            <i class="fas fa-chevron-circle-left text-dark fa-2x" id="prevBtn" style="cursor:pointer"></i>
        </div>
    <?php } ?>
    <div class="col-8 col-sm-9 col-md-10 pt-4">
        <div class="owl-carousel owl-theme d-flex justify-content-center" id="containermanagement">
            <?php
            foreach ($managementmembers as $member) {
                $clubPresidentID = $db->getColumnData("SELECT ClubPresidentID FROM clubs WHERE ClubID = ?", array($part));
                $memberID = $member->MemberID;
                $memberNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($memberID));
                $memberimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberID));
                $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($memberID));
                if (is_null($memberimg)) {
                    if ($gender == 'Erkek') {
                        $memberimg = "profilemale.png";
                    } else {
                        $memberimg = "profilefemale.png";
                    }
                }
            ?>
                <div class="d-flex flex-column justify-content-between mx-1 item carousel-div text-center friend-box" style="background-image: url('images_profile/<?= $memberimg ?>');" id="clubMember_<?= $memberID ?>">
                    <?php if ($clubPresidentID == $memberid && $memberID != $clubPresidentID) { ?>
                        <div class="row justify-content-end">
                            <button class="col-3 m-0 p-0 text-center bg-light d-flex justify-content-center align-items-center rounded-circle removeMemberDiv me-2 opt_dropdown dropbtn" memid="<?= $memberID ?>"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-content rounded-2 mt-4 px-0" style="display:none;width:280px;font-size:15px" id="opt_dropbox_<?= $memberID ?>">
                                <a href="javascript:void(0)" class="w-100 px-0 deductMember" clubmemberid="<?= $memberID ?>"><i class="fas fa-angle-double-down text-danger"></i> <?= $translates["deduct"] ?></a>
                                <a href="javascript:void(0)" class="w-100 px-0 removeMember" clubmemberid="<?= $memberID ?>"><i class="fas fa-user-slash text-danger"></i> <?= $translates["removefromclub"] ?></a>
                            </div>
                        </div>
                    <?php } ?>
                    <a class="d-flex flex-column text-center mt-auto p-3 bg-dark text-light fs-5 rounded-3 text-decoration-none" href="http://localhost/aybu/socialmedia/<?= $translates['profile'] ?>/<?= $member->MemberID ?>">
                        <span><?= $memberNames ?></span>
                        <?php if ($memberID == $clubPresidentID) { ?>
                            <small style="font-size:13px;"><?= $translates["president"] ?></small>
                        <?php } ?>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if (count($managementmembers) > 5) { ?>
        <div class="col-1 d-flex justify-content-center align-items-center">
            <i class="fas fa-chevron-circle-right text-dark fa-2x" id="nextBtn" style="cursor:pointer"></i>
        </div>
    <?php } ?>
</div>
<!-- DİĞER ÜYELER -->
<div class="row mt-5">
    <div class="col-4 mx-auto border-bottom text-dark">
        <h3 class="text-center"><?= $translates["members"] ?></h3>
    </div>
</div>
<div class="row mt-2 justify-content-center">
    <?php
    $othermembers = $db->getDatas("SELECT * FROM clubmembers WHERE ClubID = ? AND MemberPosition = ? AND Activeness = ?", array($part, "Member", 1));
    if (count($othermembers) > 5) { ?>
        <div class="col-1 d-flex justify-content-center align-items-center">
            <i class="fas fa-chevron-circle-left text-dark fa-2x" id="prevBtn" style="cursor:pointer"></i>
        </div>
    <?php } ?>
    <div class="col-8 col-sm-9 col-md-10 pt-4">
        <div class="owl-carousel owl-theme d-flex justify-content-center" id="containermembers">
            <?php
            foreach ($othermembers as $member) {
                $memberID = $member->MemberID;
                $memberNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($memberID));
                $memberimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($memberID));
                $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($memberID));
                if (is_null($memberimg)) {
                    if ($gender == 'Erkek') {
                        $memberimg = "profilemale.png";
                    } else {
                        $memberimg = "profilefemale.png";
                    }
                }
            ?>

                <div class="d-flex flex-column justify-content-between mx-1 item carousel-div text-center friend-box" style="background-image: url('images_profile/<?= $memberimg ?>');" id="clubMember_<?= $memberID ?>">
                    <?php if ($clubPresidentID == $memberid) { ?>
                        <div class="row justify-content-end">
                            <button class="col-3 m-0 p-0 text-center text-dark d-flex justify-content-center align-items-center rounded-circle removeMemberDiv me-2 opt_dropdown dropbtn" memid="<?= $memberID ?>"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-content rounded-2 mt-4 px-0" style="display:none;width:280px;font-size:15px" id="opt_dropbox_<?= $memberID ?>">
                                <a href="javascript:void(0)" class="w-100 px-0 promoteMember" clubmemberid="<?= $memberID ?>"><i class="fas fa-angle-double-up text-success"></i> <?= $translates["promotetoman"] ?></a>
                                <a href="javascript:void(0)" class="w-100 px-0 removeMember" clubmemberid="<?= $memberID ?>"><i class="fas fa-user-slash text-danger"></i> <?= $translates["removefromclub"] ?></a>
                            </div>
                        </div>
                    <?php } ?>
                    <a class="d-flex flex-column text-center mt-auto p-3 bg-dark text-light fs-5 rounded-3 text-decoration-none" href="http://localhost/aybu/socialmedia/<?= $translates['profile'] ?>/<?= $member->MemberID ?>">
                        <span><?= $memberNames ?></span>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if (count($othermembers) > 5) { ?>
        <div class="col-1 d-flex justify-content-center align-items-center">
            <i class="fas fa-chevron-circle-right text-dark fa-2x" id="nextBtn" style="cursor:pointer"></i>
        </div>
    <?php } ?>
</div>
<script>
    $(function() {
        $("#members").on("click", ".opt_dropdown", function() {
            var MemberID = $(this).attr("memid");
            $("#opt_dropbox_" + MemberID).toggle("");

        });
        $('.owl-carousel').owlCarousel({
            loop: false,
            margin: 10,
            rewind: false,
            center: false,
            autoplay: 5000,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        })
        $("#prevBtn").click(function() {
            $("#containermanagement").trigger('prev.owl.carousel', [1000]);
        });
        $("#nextBtn").click(function() {
            $("#containermanagement").trigger('next.owl.carousel', [1000]);
        });
    });
</script>