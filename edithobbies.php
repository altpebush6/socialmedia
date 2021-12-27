<form method="post" id="form_<?= $items->NavForm ?>">
    <th class="py-3 border-end"><?= $items->NavName ?></th>
    <td class="py-3">
        <div class="row flex-column">
            <div class="row flex-row">
                <div class="col-9 offset-1">
                    <input type="text" id="hobbies_input" autocomplete="off" class="form-control form-control-sm shadow" name="<?= $items->NavForm ?>" placeholder="<?= $items->NavName ?>">
                </div>
                <div class="col-1 m-0 p-0">
                    <button type="button" class="btn btn-sm rounded-3 btn-outline-theme" id="addHobby"><?= $translates["add"] ?></button>
                </div>
            </div>
            <?php $allhobbies = $db->getColumnData("SELECT MemberHobbies FROM memberabout WHERE MemberID = ?", array($memberid)); ?>
            <div class="col-9 mx-auto mt-1 text-start" id="added_hobbies" hobbies="<?= $allhobbies ?>">
                <?php
                $allhobbies = explode(",", $allhobbies);
                foreach ($allhobbies as $hobbyID => $hobby) {
                    if (!empty($hobby)) {
                ?>
                        <span class="btn btn-post btn-sm m-1" id="hobby_<?= $hobbyID ?>" style="font-size: 13px;">
                            <span><?= $hobby ?> </span>
                            <button type="button" class="btn-close" hobbyname="<?= $hobby ?>" hobbyid="<?= $hobbyID ?>" hobby="<?= $hobby ?>" style="font-size:9px;"></button>
                        </span>
                <?php }
                } ?>
            </div>
            <div class="col-10 mt-1 offset-1 text-start" id="added_hobbies">
            </div>
        </div>
    </td>
    <td class="py-3 border-start"><button type="button" class="btn btn-sm btn-outline-theme submitabout shadow" onClick='SendFormAbout("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavForm ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavForm ?>_spinner"></span></button></td>
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
                    $("#added_hobbies").append('<span class="btn btn-post btn-sm m-1" id="hobby_' + hobbyID + '" style="font-size: 13px;"><span>' + personHobby + '</span><button type="button" class="btn-close" hobbyid="' + hobbyID + '" hobbyid="' + hobbyID + '" style="font-size:9px;"></button></span>');
                    var pre_attr = $("#added_hobbies").attr("hobbies");
                    $("#added_hobbies").attr("hobbies", pre_attr + personHobby + ', ');
                    $(this).html("");
                    $(this).val("");
                }
            }
        });
        $("#addHobby").on("click", function() {
            var personHobby = $("#hobbies_input").val();
            if (personHobby != "") {
                hobbyID += 1;
                $("#added_hobbies").append('<span class="btn btn-post btn-sm m-1" id="hobby_' + hobbyID + '"  style="font-size: 13px;"><span>' + personHobby + '</span><button type="button" class="btn-close" hobbyid="' + hobbyID + '" hobbyid="' + hobbyID + '" style="font-size:9px;"></button></span>');
                var pre_attr = $("#added_hobbies").attr("hobbies");
                $("#added_hobbies").attr("hobbies", pre_attr + personHobby + ', ');
                $("#hobbies_input").html("");
                $("#hobbies_input").val("");
            }
        });
        $("#added_hobbies").on("click", ".btn-close", function() {
            var pre_attr = $("#added_hobbies").attr("hobbies");
            var hobbyID = $(this).attr("hobbyid");
            var HobbyName = $(this).attr("hobbyname");
            $.ajax({
                type: "post",
                url: "http://localhost/aybu/socialmedia/ajaxsettings.php?operation=removeJob",
                data: {
                    "Hobbies": pre_attr,
                    "RemoveHobby": HobbyName
                },
                dataType: "json",
                success: function(result) {
                    $("#added_hobbies").attr("hobbies", result.success);
                }
            });
            var hobbyID = $(this).attr("hobbyid");
            $("#hobby_" + hobbyID).remove();
        });
    });
</script>