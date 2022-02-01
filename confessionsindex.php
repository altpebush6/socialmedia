<script>
    $(function() { // İTİRAFLARI YÜKLEME
        var activeness = 'active';
        $(window).scroll(function() {
            var documentheight = $(document).height();
            var windowheight = $(window).height();
            var differance = (documentheight - windowheight);
            var lastcnfns_height = $("#containerConfessions .container:last").height();
            var scrolltop = ($(window).scrollTop() + lastcnfns_height);
            if (scrolltop > differance && activeness == 'active') {
                activeness = 'inactive';
                var id = $("#containerConfessions .container:last").attr("id");
                $.ajax({
                    type: "post",
                    url: "http://localhost/aybu/socialmedia/showconfessions.php",
                    data: {
                        "id": id,
                        "part": Part
                    },
                    dataType: "json",
                    success: function(result) {
                        if (result.state == "empty") {} else {
                            $("#containerConfessions").append(result.state);
                            activeness = "active";
                        }
                    }
                });
            }
        });
    });
</script>

<div class="container py-4 px-0 px-md-4">
    <div class="row m-0 p-0">
        <div class="col-12 m-0 p-0">
            <ul class="nav d-flex justify-content-center align-items-center" id="confessionTabs">
                <li class="nav-item" role="presentation">
                    <a class="contabsText p-3 px-5 mx-4 rounded-3 text-danger <?= ($part == $translates["love"]) ? "active opacity-10" : "opacity-8" ?>" href="http://localhost/aybu/socialmedia/<?= $translates["confessions"] . "/" . $translates["love"] ?>" id="love-tab">
                        <i class="fas fa-heart"></i> <?= $translates["Love"] ?>
                    </a>
                </li>
                <a class="contabsText text-dark mx-2 rounded-circle d-flex justify-content-center shadow align-items-center <?= ($part == $translates["all"]) ? "active" : "" ?>" id="all-tab" href="http://localhost/aybu/socialmedia/<?= $translates["confessions"] . "/" . $translates["all"] ?>" style="text-decoration:none;width:65px;height:65px">
                    <i class="fas fa-border-all" style="font-size:25px;"></i>
                </a>
                <li class="nav-item" role="presentation">
                    <a class="contabsText p-3 px-5 mx-4 rounded-3 text-danger <?= ($part == $translates["anger"]) ? "active opacity-10" : "opacity-8" ?>" href="http://localhost/aybu/socialmedia/<?= $translates["confessions"] . "/" . $translates["anger"] ?>" id="anger-tab">
                        <i class="fas fa-angry"></i> <?= $translates["Anger"] ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row p-4" id="containerConfessions">
        <div class="border col-md-7 mx-auto py-4 bg-light shadow mb-5" style="border-radius: 15px;">
            <form id="form_confession" method="post" enctype="multipart/form-data" class="px-3">
                <div class="row">
                    <h3 class="header text-center mb-3 create-post-header" style="font-family: 'Lora', serif;"><?= $translates["confess"] ?></h3>
                </div>
                <div class="row mt-2">
                    <div class="col-2 text-center flex-column">
                        <input type="hidden" id="ppOrj" pp="<?= $profile_photo; ?>">
                        <img src="images_profile/<?= $profile_photo; ?>" id="profileImage" class="rounded-circle" width="50" height="50" oncontextmenu="return false" onselectstart="return false" ondragstart="return false">
                    </div>
                    <div class="col-10">
                        <textarea class="form-control-plaintext " name="text_confession" id="text_confession" rows="4" cols="80" maxlength="250" placeholder="<?= $translates["confesssth"] ?>"></textarea>
                    </div>
                </div>
                <div class="col-10 mx-auto mt-2">
                    <div class="row">
                        <div class="col-8">
                            <select name="visibilityOpt" id="visibilityOpt" class="form-select form-select-sm">
                                <option value="0" selected><?= $translates["anonymous"] ?></option>
                                <option value="1"><?= $translates["withyourprofile"] ?></option>
                            </select>
                        </div>
                        <div class="col-4">
                            <select name="topicOpt" id="topicOpt" class="form-select form-select-sm">
                                <option value="0" selected><?= $translates["Love"] ?></option>
                                <option value="1"><?= $translates["Anger"] ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-10 mx-auto">
                        <button type="submit" class="btn w-100 rounded-3 border fs-5 shadow btn-post" name="submitconfession" id="submitconfession"><?= $translates["shareit"] ?> <span class="spinner" id="spinnercnfn"></span></button>
                    </div>
                </div>
            </form>
        </div>
        <?php

        switch ($part) {
            case $translates["love"]:
                $topic = "AND ConfessionTopic = '0'";
                break;
            case $translates["all"]:
                $topic = "";
                break;
            case $translates["anger"]:
                $topic = "AND ConfessionTopic = '1'";
                break;
            default:
                $topic = "";
                break;
        }
        $confessions = $db->getDatas("SELECT * FROM confessions WHERE ConfessionActive = ? $topic ORDER BY ConfessionAddTime DESC LIMIT 3", array(1));

        foreach ($confessions as $item) {
            $cnfnID = $item->ConfessionID;
            $cnfnMemberID = $item->MemberID;
            $isConffessionownerActive = $db->getColumnData("SELECT MemberConfirm FROM members WHERE MemberID = ?", array($cnfnMemberID));
            if ($isConffessionownerActive == 1) {
                $cnfn_profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($cnfnMemberID));
                $userNames = $db->getColumnData("SELECT MemberNames FROM members WHERE MemberID = ?", array($cnfnMemberID));
                $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($cnfnMemberID));

                if ($gender == 'Male') {
                    if (is_null($cnfn_profile_photo)) {
                        $cnfn_profile_photo = "profilemale.png";
                    }
                    $cnfn_anonym_photo = "profilemale.png";
                } else {
                    if (is_null($cnfn_profile_photo)) {
                        $cnfn_profile_photo = "profilefemale.png";
                    }
                    $cnfn_anonym_photo = "profilefemale.png";
                }
                $diff_cnfn = calculateTime($item->ConfessionAddTime);
        ?>

                <div class="container col-md-7 mx-auto p-0 my-4" id="<?= $cnfnID ?>">
                    <div class="border p-3 col-md-12 m-0 py-4 post bg-light shadow" style="border-radius: 15px;">
                        <div class="row mb-3">
                            <div class="col-10">
                                <a <?= ($item->ConfessionVisibility) ?  'href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $cnfnMemberID . '"' : "" ?>>
                                    <div class="row justify-content-center">
                                        <div class="col-2 text-end">
                                            <a <?= ($item->ConfessionVisibility) ?  'href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $cnfnMemberID . '"' : "" ?>>
                                                <img src="images_profile/<?= ($item->ConfessionVisibility) ?  $cnfn_profile_photo : $cnfn_anonym_photo ?>" class="rounded-circle" width="50" height="50">
                                            </a>
                                        </div>
                                        <div class="col-10 ps-3 p-md-0 ">
                                            <a class="text-decoration-none text-dark" <?= ($item->ConfessionVisibility) ?  'href="http://localhost/aybu/socialmedia/' . $translates['profile'] . '/' . $cnfnMemberID . '"' : "" ?>>
                                                <?= ($item->ConfessionVisibility) ?  $userNames : $translates["anonymous"] ?> <br><small><?= $diff_cnfn ?></small>
                                            </a>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-2">
                                <div class="dropdown-post">
                                    <button class="dropbtn btn rounded-circle btn-post"><i class="fas fa-ellipsis-h"></i></button>
                                    <div class="dropdown-content" style="width:220px;">
                                        <?php if ($item->MemberID == $memberid) { ?>
                                            <a href="javascript:void(0)" onClick="OpenEditConfession('<?= $cnfnID ?>','<?= $item->ConfessionText ?>')"><i class="far fa-edit"></i> <?= $translates["editcnfn"] ?></a>
                                            <a href="javascript:void(0)" onClick="DeleteConfession('deleteconfession','<?= $memberid ?>','<?= $cnfnID ?>')"><i class="far fa-trash-alt"> <?= $translates["deletecnfn"] ?></i></a>
                                            <?php
                                        } else {
                                            $diduRep = $db->getData("SELECT * FROM reports_cnfn WHERE ReporterID = ? AND ReportedID = ?", array($memberid, $cnfnID));
                                            if ($diduRep) {
                                            ?>
                                                <a href="javascript:void(0)" class="text-success unreportCnfn" postid="<?= $cnfnID ?>" id="Report_Cnfn_<?= $cnfnID ?>"><i class="fas fa-headset"></i> <?= $translates["reportedcnfn"] ?></a>
                                            <?php } else { ?>
                                                <a href="javascript:void(0)" class="text-danger reportCnfn" postid="<?= $cnfnID ?>" id="Report_Cnfn_<?= $cnfnID ?>"><i class="fas fa-bug"></i> <?= $translates["reportcnfn"] ?></a>
                                        <?php }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- İtiraf Metni -->
                        <div class="text-break fs-6 cnfnmiddle_<?= $cnfnID ?>" style="user-select:text" id="cnfnmiddle_<?= $cnfnID ?>">
                            <span id="cnfn_text_<?= $cnfnID ?>" class="ps-4 my-3"><?= $item->ConfessionText ?></span>
                        </div>
                        <!-- İtiraf Düzenleme -->
                        <div class="d-none" id="editCnfn_<?= $cnfnID ?>">
                            <form id='form_editCnfn_<?= $cnfnID ?>' class='form_edit' method='post' enctype='multipart/form-data'>
                                <div class="col-10 mx-auto my-3">
                                    <input autocomplete="off" type='text' class="form-control-plaintext" name='edittedtext' id='edittedtext_<?= $cnfnID ?>' value='<?= $item->ConfessionText ?>'>
                                </div>
                                <div class="col-10 mx-auto mt-2">
                                    <div class="row">
                                        <div class="col-8">
                                            <select name="edittedVisibilityOpt" id="edittedVisibilityOpt_<?= $cnfnID ?>" class="form-select form-select-sm">
                                                <option value="0" <?= ($item->ConfessionVisibility) ?  "" : "selected" ?>><?= $translates["anonymous"] ?></option>
                                                <option value="1" <?= ($item->ConfessionVisibility) ?  "selected" : "" ?>><?= $translates["withyourprofile"] ?></option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select name="edittedTopicOpt" id="edittedTopicOpt_<?= $cnfnID ?>" class="form-select form-select-sm">
                                                <option value="0" <?= ($item->ConfessionTopic) ?  "" : "selected" ?>><?= $translates["Love"] ?></option>
                                                <option value="1" <?= ($item->ConfessionTopic) ?  "selected" : "" ?>><?= $translates["Anger"] ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-10 mx-auto">
                                        <button type="submit" class="btn w-100 rounded-3 border fs-5 saveedit shadow btn-post" name='saveedit' idsi="<?= $cnfnID ?>" id='saveedit_<?= $cnfnID ?>'><?= $translates["shareit"] ?> <span class="spinner" id="spinnercnfnedit"></span></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
            }
        } ?>
    </div>
</div>

<script>
    function OpenEditConfession(CnfnID, TextValue) {
        event.preventDefault();
        $("#cnfnmiddle_" + CnfnID).removeClass("d-flex");
        $("#cnfnmiddle_" + CnfnID).addClass("d-none");
        $("#editCnfn_" + CnfnID).removeClass("d-none");
        $("#editCnfn_" + CnfnID).addClass("d-block");
    }
</script>