<div class="row">
    <div class="col-12 border bg-light p-4">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="my-4">All Members</h2>
            </div>
            <div class="col-12">
                <input type="text" class="form-control" id="FilterMembers" placeholder="Search Members">
            </div>
            <div class="col-12 py-3 membertable">
                <table class="table table-bordered bg-white table-striped">
                    <thead>
                        <tr>
                            <td class="text-end">MemberID</td>
                            <td>MemberEmail</td>
                            <td>MemberName</td>
                            <td>MemberGender</td>
                            <td>MemberConfirm</td>
                            <td class="text-center">Edit</td>
                            <td class="text-center">Delete</td>
                        </tr>
                    </thead>
                    <tbody id="Member_Table">
                        <?php

                        $allMembers = $db->getDatas("SELECT * FROM members");

                        foreach ($allMembers as $member) {
                            $memberid = $member->MemberID;
                        ?>

                            <tr id="member_info_<?= $memberid ?>" <?php echo ($member->MemberConfirm != 1 ? "class='bg-danger text-light'" : "") ?>>
                                <form id="membersformid_<?= $memberid ?>" method="post">
                                    <td class="text-end"><?= $memberid ?></td>
                                    <td>
                                        <span class="span_<?= $memberid ?>" id="span_Email_<?=$memberid?>"><?= $member->MemberEmail ?></span>
                                        <input type="text" name="form_Email" value="<?= $member->MemberEmail ?>" class="form-control form-control-sm d-none input_<?= $memberid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $memberid ?>" id="span_Names_<?=$memberid?>"><?= $member->MemberNames ?></span>
                                        <input type="text" name="form_Names" value="<?= $member->MemberNames ?>" class="form-control form-control-sm d-none input_<?= $memberid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $memberid ?>" id="span_Gender_<?=$memberid?>"><?= $member->MemberGender ?></span>
                                        <input type="text" name="form_Gender" value="<?= $member->MemberGender ?>" class="form-control form-control-sm d-none input_<?= $memberid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $memberid ?>" id="span_Confirm_<?=$memberid?>"> <?= $member->MemberConfirm ?> </span>
                                        <input type="text" name="form_Confirm" id="input_Confirm_<?=$memberid?>" value="<?= $member->MemberConfirm ?>" class="form-control form-control-sm d-none input_<?= $memberid ?>">
                                    </td>
                                    <td class="text-center" id="edit_<?= $memberid ?>">
                                        <i class="fas fa-user-edit editItem" id="editMember_<?=$memberid?>" onClick="openeditMember('<?= $memberid ?>')"></i>
                                        <button type="button" class="btn btn-sm btn-outline-dark d-none editbtn_<?=$memberid?>" onClick="editMember('<?=$memberid?>','membersformid_<?= $memberid ?>')"><?= $translates["save"] ?><span class="spinner" id="edit_spinner"></span></button>
                                    </td>
                                    <td class="text-center"><i class="fas fa-user-slash delItem" onClick="deleteMember('<?= $memberid ?>')"></i></td>
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
    function openeditMember(MemberID) {
        $('.input_' + MemberID).removeClass("d-none");
        $('.input_' + MemberID).addClass("d-inline-table");
        $('.span_' + MemberID).removeClass("d-inline-table");
        $('.span_' + MemberID).addClass("d-none");
        $('#editMember_' + MemberID).addClass("d-none");
        $('.editbtn_' + MemberID).removeClass("d-none");
        $('.editbtn_' + MemberID).addClass('d-inline-table');
    }

    $(function() {
        $("#FilterMembers").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#Member_Table tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>