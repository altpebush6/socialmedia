<div class="col-12 text-center">
    <h2 class="my-4">Messages</h2>
</div>
<div class="col-12 py-3 dashboard-item">
    <table class="table table-bordered bg-white table-striped">
        <thead>
            <tr>
                <td class="text-end">MessageID</td>
                <td width="50">MessageText</td>
                <td>MessageImg</td>
                <td>MessageFromID</td>
                <td>MessageToID</td>
                <td>MessageStatus</td>
                <td class="text-center">Edit</td>
                <td class="text-center">Delete</td>
            </tr>
        </thead>
        <tbody>
            <?php

            $allMessages = $db->getDatas("SELECT * FROM messages");

            foreach ($allMessages as $message) {
                $messageid = $message->MessageID;
                if(empty($message->MessageImg)){
                    $message->MessageImg = "null";
                }
            ?>

                <tr id="message_info_<?= $messageid ?>" <?php echo ($message->MessageStatus != 1 ? "class='bg-danger text-light'" : "") ?>>
                    <form id="messageformid_<?= $messageid ?>" method="post">
                        <td class="text-end"><?= $messageid ?></td>
                        <td width="50">
                            <span class="span_<?= $messageid ?>" id="span_MessageText_<?= $messageid ?>"><?= $message->MessageText ?></span>
                            <input type="text" name="form_MessageText" value="<?= $message->MessageText ?>" class="form-control form-control-sm d-none input_<?= $messageid ?>">
                        </td>
                        <td>
                            <span class="span_<?= $messageid ?>" id="span_MessageImg_<?= $messageid ?>"><?= $message->MessageImg ?></span>
                            <input type="text" name="form_MessageImg" value="<?= $message->MessageImg ?>" class="form-control form-control-sm d-none input_<?= $messageid ?>">
                        </td>
                        <td>
                            <span class="span_<?= $messageid ?>" id="span_MessageFromID_<?= $messageid ?>"><?= $message->MessageFromID ?></span>
                            <input type="text" name="form_MessageFromID" value="<?= $message->MessageFromID ?>" class="form-control form-control-sm d-none input_<?= $messageid ?>">
                        </td>
                        <td>
                            <span class="span_<?= $messageid ?>" id="span_MessageToID_<?= $messageid ?>"> <?= $message->MessageToID ?> </span>
                            <input type="text" name="form_MessageToID" value="<?= $message->MessageToID ?>" class="form-control form-control-sm d-none input_<?= $messageid ?>">
                        </td>
                        <td>
                            <span class="span_<?= $messageid ?>" id="span_MessagesStatus_<?= $messageid ?>"> <?= $message->MessageStatus ?> </span>
                            <input type="text" name="form_MessageStatus" id="MessagesStatusInput_<?=$messageid?>" value="<?= $message->MessageStatus ?>" class="form-control form-control-sm d-none input_<?= $messageid ?>">
                        </td>
                        <td class="text-center" id="edit_<?= $messageid ?>">
                            <i class="fas fa-edit editItem" id="editMessage_<?= $messageid ?>" onClick="openeditMessage('<?= $messageid ?>')"></i>
                            <button type="button" class="btn btn-sm btn-outline-dark d-none editbtn_<?= $messageid ?>" onClick="editMessage('<?= $messageid ?>','messageformid_<?= $messageid ?>')"><?= $translates["save"] ?><span class="spinner" id="messageedit_spinner"></span></button>
                        </td>
                        <td class="text-center"><i class="far fa-trash-alt delItem" onClick="deleteMessage('<?= $messageid ?>')"></i></td>
                    </form>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    function openeditMessage(MessageID) {
        $('.input_' + MessageID).removeClass("d-none");
        $('.input_' + MessageID).addClass("d-inline-table");
        $('.span_' + MessageID).removeClass("d-inline-table");
        $('.span_' + MessageID).addClass("d-none");
        $('#editMessage_' + MessageID).addClass("d-none");
        $('.editbtn_' + MessageID).removeClass("d-none");
        $('.editbtn_' + MessageID).addClass('d-inline-table');
    }
</script>