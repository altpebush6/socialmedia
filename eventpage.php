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
if ($myEvent->EventOrganizerID == $memberid) {
    $isOrganizer = 1;
} else {
    $isOrganizer = 0;
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
                            if ($phoneNum[0] == "0") {
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
                    if ($isOrganizer) { ?>
                        <button type="button" class="btn btn-primary ms-2 w-70" id="editEventBtn" eventid="<?= $myEvent->EventID ?>" data-bs-toggle="modal" data-bs-target="#editEvent"><?= $translates["editevent"] ?> <span class="spinner" id="spinnerEditEvent"></span></button>
                        <?php } else {
                        $isjoined = $db->getData("SELECT * FROM eventparticipants WHERE MemberID = ? AND EventID = ?", array($memberid, $EventID));
                        if (!$isjoined) { ?>
                            <button type="button" class="btn btn-secondary ms-2 w-70" id="joinEvent" eventid="<?= $myEvent->EventID ?>"><?= $translates["jointoevent"] ?> <span class="spinner" id="spinnerJoin"></span></button>
                        <?php } else { ?>
                            <button type="button" class="btn btn-success ms-2 w-70" id="cancelJoin" eventid="<?= $myEvent->EventID ?>"><?= $translates["joinedtoevent"] ?> <span class="spinner" id="spinnercancelJoin"></span></button>
                    <?php }
                    } ?>
                    <button type="button" class="btn btn-dark w-25"><?= $myEvent->EventPrice ?>₺</button>
                </div>
                <?php if ($isOrganizer) {
                    if ($myEvent->EventPremium) { ?>
                        <div class="col-12 my-3 mb-2 ms-0 ps-1 pe-4 mx-auto proBtn">
                            <button type="button" class="btn btn-warning ms-2 w-100" style="box-shadow: 0px 0px 2px 1px #fff;">Premium Etkinlik <i class="fas fa-check"></i></button>
                        </div> <?php } else { ?>
                        <div class="col-12 my-3 mb-2 ms-0 ps-1 pe-4 mx-auto proBtn">
                            <button type="button" class="btn btn-warning ms-2 w-100" style="box-shadow: 0px 0px 2px 1px #fff;" id="getPremium" eventid="<?= $myEvent->EventID ?>">Premium Al <i class="far fa-star"></i><span class="spinner" id="spinnerProEvent"></span></button>
                        </div>
                <?php }
                        } ?>
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
<!-- ETKİNLİĞİ DÜZENLE -->
<div class="modal fade" id="editEvent">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEventLabel"><?= $translates["editevent"] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:75vh;overflow-y:auto">
                <form method="post" id="form_createEvent" autocomplete="off">
                    <div id="first_sec">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="eventHeader" class="form-label text-muted"><?= $translates["eventheader"] ?>*</label>
                                <input class="form-control" value="<?= $myEvent->EventHeader ?>" type="text" name="eventHeader" id="eventHeader" maxlength="100" placeholder="<?= $translates["entereventheader"] ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="eventImg" class="form-label text-muted"><?= $translates["eventimage"] ?>*</label>
                                <input class="form-control" id="eventImg" name="eventImg" type="file">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="eventCategory" class="form-label text-muted"><?= $translates["eventcategory"] ?>*</label>
                                <select class="form-select" name="eventCategory" id="eventCategory">
                                    <option value="<?= $myEvent->EventCategory ?>" selected><?= ($myEvent->EventCategory) ? $db->getColumnData("SELECT CategoryName FROM eventcategories_$language WHERE CategoryID = ?", array($myEvent->EventCategory)) : $translates["selectCategory"]  ?></option>
                                    <?php
                                    $categories = $db->getDatas("SELECT * FROM eventcategories_$language");
                                    foreach ($categories as $category) { ?>
                                        <option value="<?= $category->CategoryID ?>"><?= $category->CategoryName ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="second_sec" class="d-none">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="eventSchool" class="form-label text-muted"><?= $translates["eventschool"] ?></label>
                                <select class="form-select" name="eventSchool" id="eventSchool">
                                    <option value="<?= $myEvent->EventSchool ?>" selected><?= ($myEvent->EventSchool) ? $db->getColumnData("SELECT UniversityName FROM universities WHERE UniversityID = ?", array($myEvent->EventSchool)) : $translates["selectschool"]  ?></option>
                                    <?php
                                    $universities = $db->getDatas("SELECT * FROM universities ORDER BY UniversityName ASC");
                                    foreach ($universities as $university) { ?>
                                        <option value="<?= $university->UniversityID ?>"><?= $university->UniversityName ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1" id="cityofEvent">
                            <div class="col-12">
                                <label for="eventCity" class="form-label text-muted"><?= $translates["eventcity"] ?>*</label>
                                <select class="form-select" name="eventCity" id="eventCity">
                                    <option value="<?= $myEvent->EventCity ?>" selected><?= ($myEvent->EventCity) ? $db->getColumnData("SELECT CityName FROM cities WHERE CityID = ?", array($myEvent->EventCity)) : $translates["selectcity"]  ?></option>
                                    <?php
                                    $cities = $db->getDatas("SELECT * FROM cities");
                                    foreach ($cities as $city) { ?>
                                        <option value="<?= $city->CityID ?>"><?= $city->CityName ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="checkbox" class="form-check-input" id="noCity" name="noCity" style="cursor: pointer;">
                                <label for="noCity" class="form-check-label" style="cursor: pointer;"><?= $translates["nocity"] ?></label>
                            </div>
                        </div>
                        <div class="row mt-1" id="placeofEvent" style="display: none;">
                            <div class="col-12">
                                <input type="text" class="form-control" value="<?= $myEvent->EventPlace ?>" id="eventPlace" name="eventPlace" placeholder="<?= $translates["enterplace"] ?>">
                            </div>
                        </div>
                        <div class="row mt-3 mb-3">
                            <div class="col-12">
                                <label for="eventDate" class="form-label text-muted"><?= $translates["eventdate"] ?>*</label>
                                <input type="text" class="form-control" value="<?= $myEvent->EventDateTime ?>" id="eventDate" name="eventDate" placeholder="<?= $translates["eventdate"] ?>">
                            </div>
                        </div>
                    </div>
                    <div id="third_sec" class="d-none">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="emailAddress" class="form-label text-muted"><?= $translates["contactmail"] ?>*</label>
                                <input type="text" class="form-control" id="emailAddress" value="<?= $myEvent->OrganizerEmail ?>" name="emailAddress" placeholder="<?= $translates["youremail"] ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="phoneNum" class="form-label text-muted"><?= $translates["contactphone"] ?>*</label>
                                <input type="text" class="form-control" id="phoneNum" name="phoneNum" value="<?= $myEvent->OrganizerPhone ?>" placeholder="<?= $translates["yourphone"] ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="explanation" class="form-label text-muted"><?= $translates["explanation"] ?></label>
                                <textarea type="text" class="form-control" id="explanation" maxlength="1000" name="explanation" placeholder="<?= $translates["enterexplanation"] ?>" style="resize: none;"><?= $myEvent->EventExplanation ?></textarea>
                                <div class="form-text text-end"><span id="char_left">1000</span> <?= $translates["charleft"] ?></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="pricing" class="form-label text-muted"><?= $translates["price"] ?>*</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="<?= $myEvent->EventPrice ?>" maxlength="6" id="pricing" name="pricing" placeholder="<?= $translates["enterprice"] ?>">
                                    <div class="input-group-text">₺</div>
                                </div>
                                <div class="form-text"><?= $translates["pricenote"] ?></div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer m-0 p-0" id="footer_result" style="border:none !important;">
                <p id="result"></p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="current_sec" currentsec="first_sec">
                <button type="button" id="back_btn" class="btn btn-danger w-25 d-none"><?= $translates["back"] ?> <span class="spinner" id="spinnerback"></span></button>
                <button type="button" id="continue_btn" class="btn btn-success w-100"><?= $translates["continue"] ?> <span class="spinner" id="spinnercontinue"></span></button>
                <button type="submit" id="createEvent_btn" class="btn btn-primary w-70 d-none" operation="editEvent" eventid="<?= $EventID ?>"><?= $translates["save"] ?> <span class="spinner" id="spinnercreateEvent"></span></button>
            </div>
            </form>
        </div>
    </div>
</div>