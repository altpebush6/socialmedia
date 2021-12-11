<div class="row">
    <div class="col-12 border bg-light p-4">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="my-4">All Admins</h2>
            </div>
            <div class="col-12">
                <input type="text" class="form-control" id="FilterAdmins" placeholder="Search Admins">
            </div>
            <div class="col-12 py-3 adminstable">
                <table class="table table-bordered bg-white table-striped">
                    <thead>
                        <tr>
                            <td class="text-end">AdminID</td>
                            <td>AdminEmail</td>
                            <td>AdminNames</td>
                            <td>AdminGender</td>
                            <td>AdminConfirm</td>
                            <td class="text-center">Edit</td>
                            <td class="text-center">Delete</td>
                        </tr>
                    </thead>
                    <tbody id="Admin_Table">
                        <?php

                        $allAdmins = $db->getDatas("SELECT * FROM admins");

                        foreach ($allAdmins as $admin) {
                            $adminsid = $admin->AdminID;
                        ?>

                            <tr id="admin_info_<?= $adminsid ?>" <?php echo ($admin->AdminConfirm != 1 ? "class='bg-danger text-light'" : "") ?>>
                                <form id="adminsformid_<?= $adminsid ?>" method="post">
                                    <td class="text-end"><?= $adminsid ?></td>
                                    <td>
                                        <span class="span1_<?= $adminsid ?>" id="span_Email_<?= $adminsid ?>"><?= $admin->AdminEmail ?></span>
                                        <input type="text" name="form_Email" value="<?= $admin->AdminEmail ?>" class="form-control form-control-sm d-none input1_<?= $adminsid ?>">
                                    </td>
                                    <td>
                                        <span class="span1_<?= $adminsid ?>" id="span_Names_<?= $adminsid ?>"><?= $admin->AdminNames ?></span>
                                        <input type="text" name="form_Names" value="<?= $admin->AdminNames ?>" class="form-control form-control-sm d-none input1_<?= $adminsid ?>">
                                    </td>
                                    <td>
                                        <span class="span1_<?= $adminsid ?>" id="span_Gender_<?= $adminsid ?>"><?= $admin->AdminGender ?></span>
                                        <input type="text" name="form_Gender" value="<?= $admin->AdminGender ?>" class="form-control form-control-sm d-none input1_<?= $adminsid ?>">
                                    </td>
                                    <td>
                                        <span class="span1_<?= $adminsid ?>" id="span_Confirm_<?= $adminsid ?>"> <?= $admin->AdminConfirm ?> </span>
                                        <input type="text" name="form_Confirm" id="input_Confirm_<?= $adminsid ?>" value="<?= $admin->AdminConfirm ?>" class="form-control form-control-sm d-none input1_<?= $adminsid ?>">
                                    </td>
                                    <td class="text-center" id="edit_<?= $adminsid ?>">
                                        <i class="fas fa-user-edit editItem" id="editAdmin_<?= $adminsid ?>" onClick="openeditAdmin('<?= $adminsid ?>')"></i>
                                        <button type="button" class="btn btn-sm btn-outline-dark d-none editbtn1_<?= $adminsid ?>" onClick="editAdmin('<?= $adminsid ?>','adminsformid_<?= $adminsid ?>')"><?= $translates["save"] ?><span class="spinner" id="edit_spinner"></span></button>
                                    </td>
                                    <td class="text-center"><i class="fas fa-user-slash delItem" onClick="deleteAdmin('<?= $adminsid ?>')"></i></td>
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
    function openeditAdmin(AdminID) {
        $('.input1_' + AdminID).removeClass("d-none");
        $('.input1_' + AdminID).addClass("d-inline-table");
        $('.span1_' + AdminID).removeClass("d-inline-table");
        $('.span1_' + AdminID).addClass("d-none");
        $('#editAdmin_' + AdminID).addClass("d-none");
        $('.editbtn1_' + AdminID).removeClass("d-none");
        $('.editbtn1_' + AdminID).addClass('d-inline-table');
    }

    $(function() {
        $("#FilterAdmins").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#Admin_Table tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>