<?php if ($memberid == $club->ClubPresidentID) { ?>
    <div class="container py-4 px-0 px-md-4  p-0 m-0">
        <div class="create-post border rounded-1 col-md-10 offset-md-1 py-4 shadow">
            <form id="form_event" method="post" enctype="multipart/form-data" class="px-3" autocomplete="off">
                <div class="row">
                    <h3 class="header text-center mb-3 create-post-header" style="font-family: 'Lora', serif;"><?= $translates["createevent"] ?></h3>
                </div>
                <div class="row my-2 justify-content-center">
                    <div class="d-none d-md-block col-3">
                        <label for="eventtopic" class="col-form-label text-dark fs-5"><?= $translates["eventtopic"] ?>*</label>
                    </div>
                    <div class="col-md-6 px-4 p-md-0">
                        <label for="eventtopic" class="form-label d-md-none text-dark"><?= $translates["eventtopic"] ?>*</label>
                        <input type="text" maxlength="255" id="eventtopic" name="eventtopic" class="form-control event" placeholder="<?= $translates["eventtopic"] ?>">
                    </div>
                </div>
                <div class="row my-2 justify-content-center">
                    <div class="d-none d-md-block col-3">
                        <label for="eventdate" class="col-form-label text-dark fs-5"><?= $translates["eventdate"] ?>*</label>
                    </div>
                    <div class="col-md-6 px-4 p-md-0">
                        <label for="eventtopic" class="form-label d-md-none text-dark"><?= $translates["eventdate"] ?>*</label>
                        <input class="form-control event" id="eventdate" name="eventdate" placeholder="Select date & time" type="date" value="2021-01-01">
                    </div>
                </div>
                <div class="row my-2 justify-content-center">
                    <div class="d-none d-md-block col-3">
                        <label for="eventtime" class="col-form-label text-dark fs-5"><?= $translates["eventtime"] ?>*</label>
                    </div>
                    <div class="col-md-6 px-4 p-md-0">
                        <label for="eventtopic" class="form-label d-md-none text-dark"><?= $translates["eventtime"] ?>*</label>
                        <input type="text" maxlength="5" id="eventtime" name="eventtime" class="form-control event" placeholder="12:30">
                    </div>
                </div>
                <div class="row my-2 justify-content-center">
                    <div class="d-none d-md-block col-3">
                        <label for="eventplace" class="col-form-label text-dark fs-5"><?= $translates["eventplace"] ?>*</label>
                    </div>
                    <div class="col-md-6 px-4 p-md-0">
                        <label for="eventtopic" class="form-label d-md-none text-dark"><?= $translates["eventplace"] ?>*</label>
                        <input type="text" maxlength="100" id="eventplace" name="eventplace" class="form-control event" placeholder="<?= $translates["eventplace"] ?>">
                    </div>
                </div>
                <div class="row my-2 justify-content-center">
                    <div class="d-none d-md-block col-3">
                        <label for="eventfor" class="col-form-label text-dark fs-5"><?= $translates["eventfor"] ?>*</label>
                    </div>
                    <div class="col-md-6 px-4 p-md-0">
                        <label for="eventtopic" class="form-label d-md-none text-dark"><?= $translates["eventfor"] ?>*</label>
                        <input type="text" maxlength="100" id="eventfor" name="eventfor" class="form-control event" placeholder="<?= $translates["eventforwho"] ?>">
                    </div>
                </div>
                <div class="row my-2 justify-content-center">
                    <div class="d-none d-md-block col-3">
                        <label for="eventnote" class="col-form-label text-dark fs-5"><?= $translates["eventnote"] ?></label>
                    </div>
                    <div class="col-md-6 px-4 p-md-0">
                        <label for="eventtopic" class="form-label d-md-none text-dark"><?= $translates["eventnote"] ?>*</label>
                        <input type="text" maxlength="255" id="eventnote" name="eventnote" class="form-control event" placeholder="<?= $translates["eventnote"] ?>">
                    </div>
                </div>
                <div class="row mt-3 justify-content-center">
                    <div class="col-11 col-md-5 mt-2 mb-3 px-2 m-md-0 p-md-0">
                        <p id="result"></p>
                    </div>
                    <div class="col-11 col-md-4 m-0 text-end">
                        <button type="submit" class="btn btn-post shadow w-100 mx-auto rounded-3 border fs-5 text-light" name="submitevent" id="submitevent" clubid="<?= $club->ClubID ?>"><?= $translates["shareevent"] ?> <span class="spinner" id="spinnerevent"></span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } ?>
<?php
$allevents = $db->getDatas("SELECT * FROM clubevents WHERE EventClubID = ? ORDER BY EventID DESC LIMIT 3", array($part));
foreach ($allevents as $event) {
    $creatorID = $event->EventCreatorID;
    $creatorNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($creatorID));
    $creatorimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($creatorID));
    if (is_null($creatorimg)) {
        if ($gender == 'Erkek') {
            $creatorimg = "profilemale.png";
        } else {
            $creatorimg = "profilefemale.png";
        }
    }
?>
    <div class="container p-0 m-0 my-4 px-4" id="<?= $creatorID ?>">
        <div class="create-post border rounded-1 col-md-10 mx-auto p-4 shadow">
            <div class="row d-flex justify-content-center">
                <div class="col-3 col-md-1 m-0 p-0 ps-md-2">
                    <img src="images_profile/<?= $creatorimg ?>" class="rounded-circle" style="width: 60px;height:60px;">
                </div>
                <div class="col-5 d-flex d-md-none align-items-center m-0 p-0"><h4><b><?= $event->EventTopic ?></b></h4></div>
                <div class="col-3 d-flex d-md-none align-items-center mb-3 p-0"><span class="text-dark text-center fs-5">~<?= $creatorNames ?></span></div>
                <div class="col-md-11">
                    <div class="row">
                        <div class="col-12 d-none d-md-flex flex-row justify-content-between">
                            <h4><b><?= $event->EventTopic ?></b></h4>
                            <span class="text-dark fs-5">~<?= $creatorNames ?></span>
                        </div>
                        <div class="col-12">
                            <span class="text-dark"><b><?= $translates["eventdatetime"] ?>:</b> <?= $event->EventDateTime ?></span>
                        </div>
                        <div class="col-12">
                            <span class="text-dark"><b><?= $translates["eventplace2"] ?>:</b> <?= $event->EventPlace ?></span>
                        </div>
                        <div class="col-12">
                            <span class="text-dark"><b><?= $translates["eventfor"] ?>:</b> <?= $event->EventFor ?></span>
                        </div>
                        <?php if ($event->EventNote) { ?>
                            <div class="col-12">
                                <span class="text-dark"><b><?= $translates["eventnote"] ?>:</b> <?= $event->EventNote ?></span>
                            </div>
                        <?php } ?>
                        <div class="col-12 text-end my-2 my-md-0">
                            <span class="text-dark me-4" id="eventparticipant_<?= $event->EventID ?>"><?= $translates["eventparticipant"] ?>: <?= $event->ParticipantNumber ?></span>
                            <?php
                            $isjoined = $db->getData("SELECT * FROM clubeventparticipants WHERE MemberID = ? AND EventID = ?", array($memberid, $event->EventID));
                            if ($isjoined) {
                                $join_btn = '<button type="button" class="btn btn-primary canceljoin" eventid="' . $event->EventID . '" id="canceljoin_' . $event->EventID . '">' . $translates["canceljoin"] . ' <span class="spinner" id="spinnercanceljoin"></span></button>';
                            } else {
                                $join_btn = '<button type="button" class="btn btn-success joinevent" eventid="' . $event->EventID . '" id="joinevent_' . $event->EventID . '">' . $translates["join"] . ' <span class="spinner" id="spinnerjoin"></span></button>';
                            }
                            ?>
                            <?= $join_btn ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php } ?>