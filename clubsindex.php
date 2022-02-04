<?php if ($part) {
    $club = $db->getData("SELECT * FROM clubs WHERE ClubID = ?", array($part));
    $clubpresident = $db->getData("SELECT * FROM members WHERE MemberID = ?", array($club->ClubPresidentID));
    $counterMember = $db->getColumnData("SELECT COUNT(*) FROM clubmembers WHERE ClubID = ? AND Activeness = ?", array($club->ClubID, 1));
    $clubscope = $db->getColumnData("SELECT ScopeName FROM clubscopes_$language WHERE ScopeID = ?", array($club->ClubScope));
?>
    <script>
        //Post kısmı autoload
        var Part = '<?php
                    if ($part) {
                        echo $part;
                    } else {
                        echo "0";
                    }
                    ?>';
        $(function() { // POSTLARI YÜKLEME
            var activeness = 'active';
            $(window).scroll(function() {
                var documentheight = $(document).height();
                var windowheight = $(window).height();
                var differance = (documentheight - windowheight);
                var lastposts_height = $("#posts_container .container:last").height();
                var scrolltop = ($(window).scrollTop() + lastposts_height);
                if (scrolltop > differance && activeness == 'active') {
                    activeness = 'inactive';
                    var id = $("#posts_container .container:last").attr("id");
                    $.ajax({
                        type: "post",
                        url: "http://localhost/aybu/socialmedia/showposts.php?From=Clubs",
                        data: {
                            "id": id,
                            "part": Part
                        },
                        dataType: "json",
                        success: function(result) {
                            if (result.state == "empty") {} else {
                                $("#posts_container").append(result.state);
                                activeness = "active";
                            }
                        }
                    });
                }
            });
        });
        $(function() { // ETKİNLİKLERİ YÜKLEME
            var event_activeness = 'active';
            $(window).scroll(function() {
                var documentheight = $(document).height();
                var windowheight = $(window).height();
                var differance = (documentheight - windowheight);
                var lastposts_height = $("#events .container:last").height();
                var scrolltop = ($(window).scrollTop() + lastposts_height);
                if (scrolltop > differance && event_activeness == 'active') {
                    event_activeness = 'inactive';
                    var id = $("#allEvents .container:last").attr("id");
                    $.ajax({
                        type: "post",
                        url: "http://localhost/aybu/socialmedia/showevents.php",
                        data: {
                            "id": id,
                            "part": Part
                        },
                        dataType: "json",
                        success: function(result) {
                            if (result.state == "empty") {} else {
                                $("#allEvents").append(result.state);
                                event_activeness = "active";
                            }
                        }
                    });
                }
            });
        });
    </script>
    <div class="container rounded-3 border-md p-md-5 mt-5 shadow" id="container">
        <div class="row p-0 m-0">
            <div class="col-md-9 text-center">
                <div class="row">
                    <div class="col-12 p-2 border-md-bottom">
                        <h2 class="text-dark" style="font-family: 'Nanum Gothic', sans-serif;"><?= $club->ClubName ?></h2>
                    </div>
                    <div class="col-12 text-center d-md-none">
                        <img src="club_images/<?= $club->ClubImg ?>" class="rounded-3" style="width:250px;">
                    </div>
                    <div class="col-12 p-3 border-bottom text-dark fs-5">
                        <label><b><?= $translates["clubpresident"] ?>:</b></label>
                        <span class="ms-2"> <?= $clubpresident->MemberNames ?></span>
                    </div>
                    <div class="col-12 p-3 border-bottom text-dark fs-5">
                        <label><b><?= $translates["clubmembernumber"] ?>:</b></label>
                        <span class="ms-2" id="number_member"> <?= $counterMember ?></span>
                    </div>
                    <div class="col-12 p-3 text-dark fs-5">
                        <label><b><?= $translates["clubscope"] ?>:</b></label>
                        <span class="ms-2"> <?= $clubscope ?></span>
                    </div>
                </div>
            </div>
            <div class="col-3 text-end d-none d-md-block">
                <img src="club_images/<?= $club->ClubImg ?>" class="rounded-3 shadow-lg" style="width:250px;">
            </div>
        </div>
        <div class="row mt-3 mb-1 py-4">
            <div class="col-md-8 d-flex d-md-block justify-content-center social-apps">
                <a href="https://www.facebook.com" class="text-dark"><i class="fab fa-facebook-square fa-2x mx-2 mx-md-3"></i></a>
                <a href="https://twitter.com" class="text-dark"><i class="fab fa-twitter fa-2x mx-2 mx-md-3"></i></a>
                <a href="https://www.instagram.com" class="text-dark"><i class="fab fa-instagram fa-2x mx-2 mx-md-3"></i></a>
                <a href="https://web.whatsapp.com" class="text-dark"><i class="fab fa-whatsapp fa-2x mx-2 mx-md-3"></i></a>
                <a href="https://www.linkedin.com" class="text-dark"><i class="fab fa-linkedin fa-2x mx-2 mx-md-3"></i></a>
                <a href="https://discord.com" class="text-dark"><i class="fab fa-discord fa-2x mx-2 mx-md-3"></i></a>
            </div>
            <div class="col-md-4 my-4 my-md-0 d-flex flex-row justify-content-end-md pe-4">
                <?php
                $ismember = $db->getData("SELECT * FROM clubmembers WHERE ClubID = ? AND MemberID = ? AND Activeness = ?", array($part, $memberid, 1));
                if (!$ismember) {
                    $isreqsent = $db->getData("SELECT Activeness FROM clubmembers WHERE ClubID = ? AND MemberID = ?", array($part, $memberid));
                    if ($isreqsent) {
                        $joinbutton = '<button type="button" class="btn btn-success shadow" id="cancelreq" style="font-size:15px;">' . $translates["sentjoinreq"] . ' <span class="spinner" id="spinnercancelreq"></span></button>';
                    } else {
                        $joinbutton = '<button type="button" class="btn btn-success shadow" id="joinclub" style="font-size:15px;">' . $translates["sendjoinreq"] . ' <span class="spinner" id="spinnerjoinclub"></span></button>';
                    } ?>

                    <?= $joinbutton ?>
                    <?php
                    $isspammed = $db->getData("SELECT * FROM clubspams WHERE SpammerID = ? AND ClubID = ?", array($memberid, $part));
                    if ($isspammed) {
                        $spambutton = '<button type="button" class="btn btn-danger shadow ms-2" id="cancelspam" style="font-size:15px;">' . $translates["spammedclub"] . ' <span class="spinner" id="spinnercancelspam"></span></button>';
                    } else {
                        $spambutton = '<button type="button" class="btn btn-danger shadow ms-2" id="spamclub" style="font-size:15px;">' . $translates["spamclub"] . ' <span class="spinner" id="spinnerspamclub"></span></button>';
                    } ?>
                    <?= $spambutton ?>

                <?php } else { ?>
                    <button type="button" class="btn btn-dark shadow" id="leaveclub"><?= $translates["leaveclub"] ?></button>
                    <?php
                    $isspammed = $db->getData("SELECT * FROM clubspams WHERE SpammerID = ? AND ClubID = ?", array($memberid, $part));
                    if ($isspammed) {
                        $spambutton = '<button type="button" class="btn btn-danger shadow ms-2" id="cancelspam" style="font-size:15px;">' . $translates["spammedclub"] . ' <span class="spinner" id="spinnercancelspam"></span></button>';
                    } else {
                        $spambutton = '<button type="button" class="btn btn-danger shadow ms-2" id="spamclub" style="font-size:15px;">' . $translates["spamclub"] . ' <span class="spinner" id="spinnerspamclub"></span></button>';
                    } ?>
                    <?= $spambutton ?>
            </div>
            <div class="row m-0 p-0">
                <div class="col-12 m-0 p-0">
                    <ul class="nav nav-tabs nav-fill nav-justified mt-3" id="clubTabs">
                        <li class="nav-item" style="cursor: pointer;">
                            <a class="nav-link tabsText text-dark <?= ($edit == $translates["Posts"]) ? 'active shadow' : '' ?>" href="http://localhost/aybu/socialmedia/<?= $translates["clubs"] . "/" . $part . "/" . $translates["Posts"] ?>" id="posts_container-tab"><?= $translates["posts"] ?></a>
                        </li>
                        <li class="nav-item" style="cursor: pointer;">
                            <a class="nav-link tabsText text-dark <?= ($edit == $translates["events"]) ? 'active shadow' : '' ?>" href="http://localhost/aybu/socialmedia/<?= $translates["clubs"] . "/" . $part . "/" . $translates["events"] ?>" id="events-tab"><?= $translates["Events"] ?></a>
                        </li>
                        <li class="nav-item" style="cursor: pointer;">
                            <a class="nav-link tabsText text-dark <?= ($edit == $translates["Members"]) ? 'active shadow' : '' ?>" href="http://localhost/aybu/socialmedia/<?= $translates["clubs"] . "/" . $part . "/" . $translates["Members"] ?>" id="members-tab"><?= $translates["members"] ?></a>
                        </li>
                    </ul>
                </div>
                <div class="col-12 m-0 p-0 tab-content" id="myTabContent">
                    <?php
                    switch ($edit) {
                        case $translates["Posts"]: ?>
                            <div class="py-3" id="posts_container"><?php require_once "posts_club.php" ?></div>
                        <?php break;
                        case $translates["events"]: ?>
                            <div class="py-3" id="events"><?php require_once "clubevents.php" ?></div>
                        <?php break;
                        case $translates["Members"]: ?>
                            <div class="py-3" id="members"><?php require_once "clubmembers.php" ?></div>
                        <?php break;
                        default: ?>
                            <div class="py-3" id="posts_container"><?php require_once "posts_club.php" ?></div>
                    <?php break;
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
        </div>

    <?php } else { ?>
        <div class="container border p-4 mt-5 shadow" style="min-height: 57vh;">
            <div class="row p-0 m-0">
                <div class="col-4" id="empty"></div>
                <div class="col-12 col-md-4 my-2 my-md-0 ms-auto text-center">
                    <h2 class="text-dark"><?= $translates["allclubs"] ?></h2>
                </div>
                <div class="col-12 col-md-4 ms-auto my-2 my-md-0 text-center text-md-end">
                    <button type="button" class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#addClub"><?= $translates["addclub"] ?> <i class="fas fa-plus-circle"></i></button>
                </div>
            </div>
            <div class="row mt-4 mb-3">
                <div class="col-10 col-md-5 mx-auto">
                    <div class="input-group shadow">
                        <input type="text" class="form-control" id="search_clubs" placeholder="<?= $translates["searchclub"] ?>">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="row p-2 justify-content-center" id="all_clubs">
                <?php
                $allclubs = $db->getDatas("SELECT * FROM clubs WHERE ClubState = ? ORDER BY ClubName ASC", array(1));
                foreach ($allclubs as $club) {
                    $clubname = $club->ClubName;
                    if (strlen($clubname) > 18) {
                        $clubname = substr($clubname, 0, 18);
                        $lastletter = substr($clubname, -1);
                        if ($lastletter == " ") {
                            $clubname = substr($clubname, 0, 17);
                        }
                        $clubname .= "...";
                    }
                ?>
                    <div class="col-md-3 p-3 m-2 border shadow-lg">
                        <div class="row">
                            <div class="col-3 m-0 p-0 d-flex justify-content-center align-items-center">
                                <img src="club_images/<?= $club->ClubImg ?>" class="rounded-circle" style="width:50px;height:50px;border:2px solid rgba(255, 255, 255, 0.788);">
                            </div>
                            <div class="col-6 d-flex align-items-center">
                                <div class="col-12 fs-5 text-dark ps-1" title="<?= $club->ClubName ?>"><?= $clubname ?></div>
                            </div>
                            <div class="col-3 d-flex align-items-center">
                                <a href="http://localhost/aybu/socialmedia/<?= $translates["clubs"] ?>/<?= $club->ClubID ?>/<?= $translates["Posts"] ?>" class="btn btn-outline-dark w-100"><?= $translates["go"] ?></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- MODAL -->
        <div class="modal fade" id="addClub">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClubLabel"><?= $translates["addclub"] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="form_addClub" autocomplete="off">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="name" class="form-label text-muted"><?= $translates["clubname"] . "*" ?></label>
                                    <input class="form-control" type="text" name="clubname" id="clubname" maxlength="100" placeholder="<?= $translates["enterclubname"] ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="name" class="form-label text-muted"><?= $translates["clubscope"] . "*" ?></label>
                                    <select class="form-select" name="clubscope" id="clubscope">
                                        <option value="0" selected disabled><?= $translates["chooseclubscope"] ?></option>
                                        <?php
                                        $allscopes = $db->getDatas("SELECT * FROM clubscopes_$language");
                                        foreach ($allscopes as $scope) { ?>
                                            <option value="<?= $scope->ScopeID ?>"><?= $scope->ScopeName ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="clubimg" class="form-label text-muted"><?= $translates["clubimg"] . "*" ?></label>
                                    <input class="form-control" id="clubimg" name="clubimg" type="file">
                                </div>
                            </div>
                            <p id="result"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="addclub_btn" class="btn btn-primary w-100"><?= $translates["addclub"] ?> <span class="spinner" id="spinneraddclub"></span></button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
    <script>
        function openComments(PostID) {
            var state = $("#postcomment_" + PostID).css("display");
            if (state == "none") {
                $("#postcomment_" + PostID).css("display", "flex");
                $("#comments_" + PostID).css("display", "flex");
            } else {
                $("#postcomment_" + PostID).css("display", "none");
                $("#comments_" + PostID).css("display", "none");
            }
        }

        function OpenEditPost(PostID, TextValue) {
            event.preventDefault();
            $("#postmiddle_" + PostID).removeClass("d-flex");
            $("#postmiddle_" + PostID).addClass("d-none");
            $("#likecomment_" + PostID).css("display", "none");
            $("#addpartul_" + PostID).removeClass("d-none");
            $("#addpartul_" + PostID).addClass("d-block");
        }

        function OpenEditComment(CommentID, PostID) {
            event.preventDefault();
            $("#comment_text_" + CommentID).css("display", "none");
            $("#form_editcomment_" + CommentID).removeClass("d-none");
            $("#form_editcomment_" + CommentID).addClass("d-block");
        }
        $(function() {
            $("#image_upload").on("change", function() { //Post Atmada IMG 
                $("#warn_file").removeClass("d-block");
                $("#warn_file").addClass("d-none");
                $('#posting_img')[0].src = window.URL.createObjectURL(this.files[0]);
                if (window.URL.createObjectURL(this.files[1])) {
                    $('#review_more').removeClass("d-none");
                    $('#review_more').addClass("d-flex");
                    $('#review_more').html('<i class="fas fa-plus"></i>');
                }
                if (window.URL.createObjectURL(this.files[4])) {
                    $("#warn_file").removeClass("d-none");
                    $("#warn_file").addClass("d-block");
                    $("#warn_file").html('<?= $translates["imagelimit"] ?>');
                }
            });
            $("#file_upload").on("change", function() { //Post Atmada FILE
                $("#posting_file").html('');
                let i = 0;
                while (this.files[i]) {
                    $("#posting_file").append('<i class="fas fa-file-alt fa-2x"></i> ' + this.files[i]["name"] + '<br><br>');
                    i++;
                }
                if (window.URL.createObjectURL(this.files[4])) {
                    $("#warn_file").removeClass("d-none");
                    $("#warn_file").addClass("d-block");
                    $("#warn_file").html('<?= $translates["filelimit"] ?>');
                }
            });

            $("#posts_container").on("change", ".edit_image_upload", function() { //Post editlemede IMAGE
                var PostID = $(this).attr("postid");
                $("#edit_post_images_" + PostID).removeClass("d-flex");
                $("#edit_post_images_" + PostID).addClass("d-none");
                $('#posting_img_edit_' + PostID)[0].src = window.URL.createObjectURL(this.files[0]);
                if (window.URL.createObjectURL(this.files[1])) {
                    $('#review_more_edit_' + PostID).removeClass("d-none");
                    $('#review_more_edit_' + PostID).addClass("d-flex");
                    $('#review_more_edit_' + PostID).html('<i class="fas fa-plus"></i>');
                    if (window.URL.createObjectURL(this.files[4])) {
                        $("#warn_file_edit_" + PostID).removeClass("d-none");
                        $("#warn_file_edit_" + PostID).addClass("d-block");
                        $("#warn_file_edit_" + PostID).html('<?= $translates["imagelimit"] ?>');
                    }
                }
            });
            $("#posts_container").on("change", ".edit_file_upload", function() { //Post editlemede FILE
                var PostID = $(this).attr("postid");
                $("#edit_post_files_" + PostID).html('');
                let i = 0;
                while (this.files[i]) {
                    $("#edit_post_files_" + PostID).append('<div class="col-12 my-2 ps-4 fs-6"><i class="fas fa-file-alt fa-2x text-dark"></i> <a class="text-dark" href="">' + this.files[i]["name"] + '</a> </div>');
                    i++;
                }
                if (window.URL.createObjectURL(this.files[4])) {
                    $("#warn_file").removeClass("d-none");
                    $("#warn_file").addClass("d-block");
                    $("#warn_file").html('<?= $translates["filelimit"] ?>');
                }
            });
        });
    </script>