<form method="post" id="form_<?= $items->NavForm ?>">
    <th class="py-3 border-end"><?= $items->NavName ?></th>
    <td class="py-3">
        <div class="row flex-column">
            <div class="col-10 offset-1">
                <input type="text" id="tv_input" maxlength="150" autocomplete="off" class="form-control form-control-sm shadow" name="<?= $items->NavForm ?>" placeholder="<?= $items->NavName ?>">
            </div>
            <div class="col-10 mt-1 offset-1 text-start" id="added_tv">
            </div>
        </div>
    </td>
    <td class="py-3 border-start"><button type="button" class="btn btn-sm btn-outline-theme submitabout shadow" onClick='SendFormAbout("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavForm ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavForm ?>_spinner"></span></button></td>
</form>
<script>
    $(function() {
        var tvID = 0;
        $("#tv_input").on("keypress", function(e) {
            var persontv = $(this).val();
            if (e.which == 13) {
                e.preventDefault();
                if (persontv != "") {
                    tvID += 1;
                    $("#added_tv").append('<span class="btn btn-primary btn-sm m-1" id="tv_' + tvID + '" style="font-size: 13px;"><span>' + persontv + '</span><button type="button" class="btn-close" tvid="' + tvID + '" style="font-size:9px;"></button></span>');
                    $(this).html("");
                    $(this).val("");
                }
            }
        });
        $("#added_tv").on("click", ".btn-close", function() {
            var tvID = $(this).attr("tvid");
            $("#tv_" + tvID).remove();
        });
    });
</script>