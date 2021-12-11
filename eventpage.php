<?php

$allevents = $db->getDatas("SELECT * FROM events");

$EventID = "";

foreach ($allevents as $event) {
    $Header = $event->EventHeader;
    $seodHeader = seolink($Header) . "-" . $event->EventID;
    if ($seodHeader == $part) {
        $EventID = $event->EventID;
    }
}

$myEvent = $db->getData("SELECT * FROM events WHERE EventID = ?", array($EventID));
$organizer = $db->getData("SELECT * FROM members WHERE MemberID = ?", array($myEvent->EventOrganizerID));
$organizer_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($organizer->MemberID));
$gender = $organizer->MemberGender;

if (is_null($organizer_photo)) {
    if ($gender == 'Erkek') {
        $organizer_photo = "profilemale.png";
    } else {
        $organizer_photo = "profilefemale.png";
    }
}
?>
<div class="organizer">
    <div class="row w-100 m-0 p-0 text-light">
        <div class="col-12">
            <div class="row">
                <div class="col-12 mt-2">
                    <h5 class="text-center"><?= $translates["organizer"] ?></h5>
                </div>
                <hr class="m-0">
                <div class="col-12 my-3">
                    <a class="text-decoration-none text-light" href="http://localhost/aybu/socialmedia/<?= $translates["profile"] ?>/<?= $organizer->MemberID ?>">
                        <div class="row">
                            <div class="col-3 m-0 p-0 ps-2">
                                <img src="images_profile/<?= $organizer_photo ?>" class="rounded-circle" style="width:40px;height:40px;">
                            </div>
                            <div class="col-9 d-flex align-items-center m-0 p-0">
                                <span style="font-size: 18px;  white-space: nowrap;overflow: hidden;text-overflow: ellipsis;" title="<?= $organizer->MemberNames ?>"><?= $organizer->MemberNames ?></span>
                            </div>
                        </div>
                    </a>
                </div>
                <hr class="m-0">
                <div class="col-12 my-3">
                    <div class="row" style="font-size: 17px;">
                        <div class="col-2 m-0 p-0 text-end pe-1">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="col-10 m-0 p-0" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;" title="<?= $myEvent->OrganizerEmail ?>">
                            <?= $myEvent->OrganizerEmail ?>
                        </div>
                    </div>
                </div>
                <hr class="m-0">
                <div class="col-12 my-3">
                    <div class="row" style="font-size: 17px;">
                        <div class="col-2 m-0 p-0 text-end pe-1">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="col-10 m-0 p-0" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;" title="<?= $myEvent->OrganizerPhone ?>">
                            <?php
                            $phoneNum = $myEvent->OrganizerPhone;
                            if ($phoneNum [0] == "0") {
                                echo "(" . substr($phoneNum, 0, 4) . ")-" . substr($phoneNum, 4, 3) . "-" . substr($phoneNum, 7, 4);
                            } else {
                                echo "(0" . substr($phoneNum, 0, 3) . ")-" . substr($phoneNum, 3, 3) . "-" . substr($phoneNum, 6, 4);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5">
    <div class="row">
        <div class="col-5">
            <div class="row">
                <div class="col-12">
                    <img src="events_images/<?= $myEvent->EventImage ?>" class="w-100 rounded-3" style="max-height: 70vh;">
                </div>
                <div class="col-12 mt-3 p-0 ps-1" id="event_buttons">
                    <?php
                    $isjoined = $db->getData("SELECT * FROM eventparticipants WHERE MemberID = ? AND EventID = ?", array($memberid, $EventID));
                    if (!$isjoined) { ?>
                        <button type="button" class="btn btn-secondary ms-2 w-70" id="joinEvent" eventid="<?= $myEvent->EventID ?>"><?= $translates["jointoevent"] ?> <span class="spinner" id="spinnerJoin"></span></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-success ms-2 w-70" id="cancelJoin" eventid="<?= $myEvent->EventID ?>"><?= $translates["joinedtoevent"] ?> <span class="spinner" id="spinnercancelJoin"></span></button>
                    <?php } ?>
                    <button type="button" class="btn btn-dark w-25"><?= $myEvent->EventPrice ?>₺</button>
                </div>
                <div class="col-12 mt-1">
                    <div class="text-light form-text"><?= $translates["contactorganizer"] ?></div>
                </div>
                <div class="col-12 mt-1">
                    <div class="text-light form-text"><?= $translates["notresponsible"] ?></div>
                </div>
            </div>
        </div>
        <div class="col-7">
            <div class="row">
                <div class="col-12 mb-3">
                    <h2 class="text-center category_names"><?= $myEvent->EventHeader ?></h2>
                </div>
                <?php if ($myEvent->EventSchool) { ?>
                    <div class="col-12 text-light fs-5 border-bottom py-3"><?= $translates["university"] ?>: <?= $db->getColumnData("SELECT UniversityName FROM universities WHERE UniversityID = ?", array($myEvent->EventSchool)) ?></div>
                <?php }
                if ($myEvent->EventCity) { ?>
                    <div class="col-12 text-light fs-5 border-bottom py-3"><?= $translates["city"] ?>: <?= $db->getColumnData("SELECT CityName FROM cities WHERE CityID = ?", array($myEvent->EventCity)) ?></div>
                <?php }
                if ($myEvent->EventPlace) { ?>
                    <div class="col-12 text-light fs-5 border-bottom py-3"><?= $translates["place"] ?>: <?= $myEvent->EventPlace ?></div>
                <?php } ?>
                <div class="col-12 text-light fs-5 border-bottom py-3"><?= $translates["date"] ?>: <?= $myEvent->EventDateTime ?></div>
                <div class="col-12 text-light fs-5 border-bottom py-3"><span id="participantNum"><?= $myEvent->EventParticipant ?></span> <?= $translates["participant"] ?></div>
            </div>
        </div>
    </div>
    <div class="row mt-3 mb-4">
        <div class="col-12 fs-4 text-primary">
            <u><i><?= $translates["eventexplanation"] ?></i></u>
        </div>
        <div class="col-12 fs-5 text-light">
            <?= ($myEvent->EventExplanation) ? $myEvent->EventExplanation : $translates["noexp"] ?>
        </div>
    </div>
</div>