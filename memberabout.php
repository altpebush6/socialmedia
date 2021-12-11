<div class="row">
    <div class="col-12 border bg-light p-4">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="my-4">All Members' Abouts</h2>
            </div>
            <div class="col-8 py-3">
                <table class="table table-bordered bg-white table-striped">
                    <thead>
                        <tr>
                            <td class="text-end">AboutID</td>
                            <td>MemberID</td>
                            <td>Member's FacultyID</td>
                            <td>Member's DepartmentID</td>
                            <td>Member's Hobbies</td>
                            <td>Member's FavTV</td>
                            <td>Member's Hometown</td>
                            <td>Member's City</td>
                            <td class="text-center">Edit</td>
                        </tr>
                    </thead>
                    <tbody id="Abouts_Table">
                        <?php

                        $allAbouts = $db->getDatas("SELECT * FROM memberabout");

                        foreach ($allAbouts as $about) {
                            $aboutid = $about->AboutID;
                        ?>

                            <tr id="about_info_<?= $aboutid ?>">
                                <form id="aboutsformid_<?= $aboutid ?>" method="post">
                                    <td class="text-end"><?= $aboutid ?></td>
                                    <td><?= $about->MemberID ?></td>
                                    <td>
                                        <span class="span_<?= $aboutid ?>" id="span_Faculty_<?= $aboutid ?>"><?= $about->MemberFaculty ?></span>
                                        <input type="text" name="form_Faculty" value="<?= $about->MemberFaculty ?>" class="form-control form-control-sm d-none input_<?= $aboutid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $aboutid ?>" id="span_Department_<?= $aboutid ?>"><?= $about->MemberDepartment ?></span>
                                        <input type="text" name="form_Department" value="<?= $about->MemberDepartment ?>" class="form-control form-control-sm d-none input_<?= $aboutid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $aboutid ?>" id="span_Hobbies_<?= $aboutid ?>"><?= $about->MemberHobbies ?></span>
                                        <input type="text" name="form_Hobbies" value="<?= $about->MemberHobbies ?>" class="form-control form-control-sm d-none input_<?= $aboutid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $aboutid ?>" id="span_FavTV_<?= $aboutid ?>"><?= $about->MemberFavTV ?></span>
                                        <input type="text" name="form_FavTV" value="<?= $about->MemberFavTV ?>" class="form-control form-control-sm d-none input_<?= $aboutid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $aboutid ?>" id="span_Hometown_<?= $aboutid ?>"><?= $about->MemberHometown ?></span>
                                        <input type="text" name="form_Hometown" value="<?= $about->MemberHometown ?>" class="form-control form-control-sm d-none input_<?= $aboutid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $aboutid ?>" id="span_City_<?= $aboutid ?>"><?= $about->MemberCity ?></span>
                                        <input type="text" name="form_City" value="<?= $about->MemberCity ?>" class="form-control form-control-sm d-none input_<?= $aboutid ?>">
                                    </td>
                                    <td class="text-center" id="edit_<?= $aboutid ?>">
                                        <i class="fas fa-edit editItem" id="editabout_<?= $aboutid ?>" onClick="openeditabout('<?= $aboutid ?>')"></i>
                                        <button type="button" class="btn btn-sm btn-outline-dark d-none editbtn_<?= $aboutid ?>" onClick="editAbout('<?= $aboutid ?>','aboutsformid_<?= $aboutid ?>')"><?= $translates["save"] ?><span class="spinner" id="abouts_spinner"></span></button>
                                    </td>
                                </form>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-2 py-3">
                <h5 class="text-center">Faculties</h5>
                <ul class="list-group about_infos" style="overflow-y: auto;">
                    <?php

                    $faculties = $db->getDatas("SELECT * FROM faculties_$language");
                    foreach ($faculties as $faculty) { ?>
                        <li class="list-group-item"><?= $faculty->FacultyID . ". " . $faculty->FacultyName ?></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col-2 py-3 px-0">
                <h5 class="text-center">Departments</h5>
                <ul class="list-group about_infos" style="overflow-y: auto;">
                    <?php

                    $departments = $db->getDatas("SELECT * FROM departments_$language");
                    foreach ($departments as $department) {
                        $facultname = $db->getColumnData("SELECT FacultyName FROM faculties_$language WHERE FacultyID = ?", array($department->FacultyID)); ?>
                        <li class="list-group-item"><?= $department->DepartmentID . ". " . $facultname  . " - " . $department->DepartmentName ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="my-4">All Exchange Requests</h2>
            </div>
            <div class="col-12 py-3">
                <table class="table table-bordered bg-white table-striped">
                    <thead>
                        <tr>
                            <td class="text-end">RequestID</td>
                            <td>MemberID</td>
                            <td>RequestItem</td>
                            <td>RequestStatus</td>
                            <td class="text-center">Edit</td>
                            <td class="text-center">Delete</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $allRequests = $db->getDatas("SELECT * FROM memberaboutrequests");

                        foreach ($allRequests as $request) {
                            $requestid = $request->RequestID;
                        ?>

                            <tr id="request_info_<?= $requestid ?>" <?php echo ($request->RequestStatus != 1 ? "class='bg-danger text-light'" : "") ?>>
                                <form id="requestsformid_<?= $requestid ?>" method="post">
                                    <td class="text-end"><?= $requestid ?></td>
                                    <td>
                                        <span class="span_<?= $requestid ?>" id="span_MemberID_<?= $requestid ?>"><?= $request->MemberID ?></span>
                                        <input type="text" name="form_MemberID" value="<?= $request->MemberID ?>" class="form-control form-control-sm d-none input_<?= $requestid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $requestid ?>" id="span_RequestItem_<?= $requestid ?>"><?= $request->RequestItem ?></span>
                                        <input type="text" name="form_RequestItem" value="<?= $request->RequestItem ?>" class="form-control form-control-sm d-none input_<?= $requestid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $requestid ?>" id="span_RequestStatus_<?= $requestid ?>"><?= $request->RequestStatus ?></span>
                                        <input type="text" name="form_RequestStatus" value="<?= $request->RequestStatus ?>" class="form-control form-control-sm d-none input_<?= $requestid ?>">
                                    </td>
                                    <td class="text-center" id="edit_<?= $requestid ?>">
                                        <i class="fas fa-edit editItem" id="editRequest_<?= $requestid ?>" onClick="openeditRequest('<?= $requestid ?>')"></i>
                                        <button type="button" class="btn btn-sm btn-outline-dark d-none editbtn_<?= $requestid ?>" onClick="editRequest('<?= $requestid ?>','requestsformid_<?= $requestid ?>')"><?= $translates["save"] ?><span class="spinner" id="requests_spinner"></span></button>
                                    </td>
                                    <td class="text-center"><i class="fas fa-trash-alt delItem" onClick="deleteRequest('<?= $requestid ?>')"></i></td>
                                </form>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    var abouts_height = $("#Abouts_Table").height();
    abouts_height = abouts_height + 40;
    $(".about_infos").css("height", abouts_height);

    function openeditabout(aboutid) {
        $('.input_' + aboutid).removeClass("d-none");
        $('.input_' + aboutid).addClass("d-inline-table");
        $('.span_' + aboutid).removeClass("d-inline-table");
        $('.span_' + aboutid).addClass("d-none");
        $('#editabout_' + aboutid).addClass("d-none");
        $('.editbtn_' + aboutid).removeClass("d-none");
        $('.editbtn_' + aboutid).addClass('d-inline-table');
    }

    function openeditRequest(RequestID) {
        $('.input_' + RequestID).removeClass("d-none");
        $('.input_' + RequestID).addClass("d-inline-table");
        $('.span_' + RequestID).removeClass("d-inline-table");
        $('.span_' + RequestID).addClass("d-none");
        $('#editRequest_' + RequestID).addClass("d-none");
        $('.editbtn_' + RequestID).removeClass("d-none");
        $('.editbtn_' + RequestID).addClass('d-inline-table');
    }
</script>