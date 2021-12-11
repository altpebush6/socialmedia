<form method="post" id="form_<?= $items->NavForm ?>">
    <th class="py-3 border-end"><?= $items->NavName ?></th>
    <td class="py-3">
        <div class="row flex-column">
            <div class="col-10 offset-1">
                <input type="text" id="hobbies_input" autocomplete="off" class="form-control form-control-sm" name="<?= $items->NavForm ?>" placeholder="<?= $items->NavName ?>">
            </div>
            <div class="col-10 mt-1 offset-1 text-start" id="added_hobbies">
            </div>
        </div>
    </td>
    <td class="py-3 border-start"><button type="button" class="btn btn-sm btn-outline-primary submitabout" onClick='SendFormAbout("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavForm ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavForm ?>_spinner"></span></button></td>
</form>
<script>
    $(function() {
        var hobbyID = 0;
        $("#hobbies_input").on("keypress", function(e) {
            var personHobby = $(this).val();
            if (e.which == 13) {
                e.preventDefault();
                if (personHobby != "") {
                    hobbyID += 1;
                    $("#added_hobbies").append('<span class="btn btn-primary btn-sm m-1" id="hobby_' + hobbyID + '" style="font-size: 13px;"><span>' + personHobby + '</span><button type="button" class="btn-close" hobbyid="' + hobbyID + '" style="font-size:9px;"></button></span>');
                    $(this).html("");
                    $(this).val("");
                }
            }
        });
        $("#added_hobbies").on("click", ".btn-close", function() {
            var hobbyID = $(this).attr("hobbyid");
            $("#hobby_" + hobbyID).remove();
        });
    });
</script>