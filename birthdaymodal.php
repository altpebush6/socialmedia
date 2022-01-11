<?php
$birthday_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($dayMember->MemberID));
$gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($dayMember->MemberID));
if (is_null($birthday_profile_photo)) {
    if ($gender == 'Male') {
        $birthday_profile_photo = "profilemale.png";
    } else {
        $birthday_profile_photo = "profilefemale.png";
    }
}
?>
<div class="modal position-fixed" id="birthdayModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center">
                <i class="fas fa-birthday-cake fa-2x"></i>
                <div class="row d-flex flex-column justify-content-center align-items-center">
                    <img src="images_profile/<?= $birthday_profile_photo ?>" class="rounded-circle m-0 p-0" style="width:70px;height:70px;">
                    <h5 class="modal-title" style="font-size:24px;" id="exampleModalLabel"><?= $dayMember->MemberName . $translates["hasbirthday"] ?></h5>
                </div>
                <i class="fas fa-birthday-cake fa-2x"></i>
            </div>
            <div class="modal-body text-center fs-4 py-4 px-4">
                <?= ($dayMember->MemberGender == 'Male') ? $translates["congrathisbirthday"] : $translates["congratherbirthday"] ?>
            </div>
            <div class="modal-footer text-center px-2">
                <a class="text-decoration-none btn btn-post w-100" href="http://localhost/aybu/socialmedia/<?= $translates["messages"] ?>/<?= $dayMember->MemberID ?>"><i class="fab fa-facebook-messenger me-1"></i> <?= $translates["message"] ?></a>
            </div>
        </div>
    </div>
</div>