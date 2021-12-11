<div class="col-12 text-center">
    <h2 class="my-4">Chatboxes</h2>
</div>
<div class="col-12 py-3 dashboard-item">
    <table class="table table-bordered bg-white table-striped">
        <thead>
            <tr>
                <td class="text-end">ChatboxID</td>
                <td>MessageFromID</td>
                <td>MessageToID</td>
                <td>MessageStatus</td>
                <td class="text-center">Edit</td>
                <td class="text-center">Delete</td>
            </tr>
        </thead>
        <tbody id="chatbox_Table">
            <?php

            $chatboxes = $db->getDatas("SELECT * FROM chatbox");

            foreach ($chatboxes as $chatbox) {
                $chatboxid = $chatbox->ChatboxID;
            ?>

                <tr id="chatbox_info_<?= $chatboxid ?>" <?php echo ($chatbox->MessageStatus != 1 ? "class='bg-danger text-light'" : "") ?>>
                    <form id="Chatboxformid_<?= $chatboxid ?>" method="post">
                        <td class="text-end"><?= $chatboxid ?></td>
                        <td>
                            <span class="span_<?= $chatboxid ?>" id="span_FromID_<?= $chatboxid ?>"><?= $chatbox->MessageFromID ?></span>
                            <input type="text" name="form_FromID" value="<?= $chatbox->MessageFromID ?>" class="form-control form-control-sm d-none input_<?= $chatboxid ?>">
                        </td>
                        <td>
                            <span class="span_<?= $chatboxid ?>" id="span_ToID_<?= $chatboxid ?>"><?= $chatbox->MessageToID ?></span>
                            <input type="text" name="form_ToID" value="<?= $chatbox->MessageToID ?>" class="form-control form-control-sm d-none input_<?= $chatboxid ?>">
                        </td>
                        <td>
                            <span class="span_<?= $chatboxid ?>" id="span_MessageStatus_<?= $chatboxid ?>"> <?= $chatbox->MessageStatus ?> </span>
                            <input type="text" name="form_MessageStatus" id="MessageStatusInput_<?= $chatboxid ?>" value="<?= $chatbox->MessageStatus ?>" class="form-control form-control-sm d-none input_<?= $chatboxid ?>">
                        </td>
                        <td class="text-center" id="edit_<?= $chatboxid ?>">
                            <i class="fas fa-edit editItem" id="editChatBox_<?= $chatboxid ?>" onClick="openeditChatBox('<?= $chatboxid ?>')"></i>
                            <button type="button" class="btn btn-sm btn-outline-dark d-none editbtn_<?= $chatboxid ?>" onClick="editChatBox('<?= $chatboxid ?>','Chatboxformid_<?= $chatboxid ?>')"><?= $translates["save"] ?><span class="spinner" id="editchatbox_spinner"></span></button>
                        </td>
                        <td class="text-center"><i class="far fa-trash-alt delItem" onClick="deleteChatBox('<?= $chatboxid ?>')"></i></td>
                    </form>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    function openeditChatBox(ChatBoxID) {
        $('.input_' + ChatBoxID).removeClass("d-none");
        $('.input_' + ChatBoxID).addClass("d-inline-table");
        $('.span_' + ChatBoxID).removeClass("d-inline-table");
        $('.span_' + ChatBoxID).addClass("d-none");
        $('#editChatBox_' + ChatBoxID).addClass("d-none");
        $('.editbtn_' + ChatBoxID).removeClass("d-none");
        $('.editbtn_' + ChatBoxID).addClass('d-inline-table');
    }
</script>