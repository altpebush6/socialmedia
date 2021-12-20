<script>
    var SITE_URL = "http://localhost/aybu";

    // GİRİŞ AJAX

    function SendFormLog(FormID, Operation, SendURL = "") {
        $("#spinnerlog").html('<i class="fas fa-spinner fa-spin"></i>');
        $("#girissubmit").prop("disabled", true);
        var Datas = $("form#" + FormID).serialize();
        $.ajax({
            type: "post",
            url: SITE_URL + "/socialmedia/ajaxlogin.php?operation=" + Operation,
            data: Datas,
            dataType: "json",
            success: function(result) {
                $("#spinnerlog").html("");
                $("#girissubmit").prop("disabled", false);
                if (result.adminlogedin) {
                    window.location.href = SITE_URL + "/socialmedia/adminpaneli";
                } else {
                    if (result.error) {
                        $("#result1").html("<div class='bg-danger text-white text-center mt-2 py-2 rounded-2'>" + "<i class='fas fa-exclamation-triangle'></i> " + result.error + "</div>");
                    } else if (result.success) {
                        $("#result1").html("");
                        $("form").trigger("reset");
                        window.location.href = SITE_URL + "/socialmedia/<?= $translates["home"] ?>";
                    }
                }
            }
        });
    }
    // KAYIT
    $("#RegisterModal").on("click", "#register_btn", function() {
        var FormID = "form_register";
        var Operation = "register";
        var Datas = $("form#" + FormID).serialize();
        $("#spinnerreg").html('<i class="fas fa-spinner fa-spin"></i>');
        $("#register_btn").prop("disabled", true);
        $.ajax({
            type: "post",
            url: SITE_URL + "/socialmedia/ajaxlogin.php?operation=" + Operation,
            data: Datas,
            dataType: "json",
            success: function(result) {
                $("#spinnerreg").html("");
                $("#register_btn").prop("disabled", false);
                if (result.error) {
                    $("#result2").html("<div class='bg-danger text-white text-center mt-3 mb-1 py-2 rounded-2'>" + "<i class='fas fa-exclamation-triangle'></i> " + result.error + "</div>");
                } else if (result.success) {
                    $("form").trigger("reset");
                    $("#result2").html("<div class='bg-success text-white text-center mt-3 mb-1 py-2 rounded-2'><i class='fas fa-check-square'></i> " + result.success + "</div>");
                    $("#register_btn").attr("memberid", result.MemberID);
                    $("#register_btn").html('<?= $translates["continue"] ?> <span class="spinner" id="spinnerreg"></span>');
                    $("#register_btn").attr("id", "continue_reg");
                }
            }
        });
    });
    $("#RegisterModal").on("click", "#continue_reg", function() {
        var FormID = "form_register";
        var Operation = "register";
        var Datas = $("form#" + FormID).serialize();
        $("#spinnerreg").html('<i class="fas fa-spinner fa-spin"></i>');
        $("#register_btn").prop("disabled", true);
        $.ajax({
            type: "post",
            url: SITE_URL + "/socialmedia/ajaxlogin.php?operation=" + Operation,
            data: Datas,
            dataType: "json",
            success: function(result) {
                $("#spinnerreg").html("");
                $("#register_btn").prop("disabled", false);
                if (result.error) {
                    $("#result2").html("<div class='bg-danger text-white text-center mt-3 mb-1 py-2 rounded-2'>" + "<i class='fas fa-exclamation-triangle'></i> " + result.error + "</div>");

                } else {
                    $("#spinnerreg").html("");
                    $("#register_btn").prop("disabled", false);
                    $("#result2").html("");
                    $("#nextpart").removeClass("d-none");
                    $("#nextpart").addClass("d-block");
                    $("#form_register").removeClass("d-block");
                    $("#form_register").addClass("d-none");
                    $("#continue_reg").html('<?= $translates["completereg"] ?> <span class="spinner" id="spinnerreg"></span>');
                    $("#continue_reg").attr("id", "complete_reg");
                }
            }
        });

    });
    $("#MemberFaculty").on("change", function() {
        var chosenFaculty = $(this).val();
        $.ajax({
            type: "post",
            url: SITE_URL + "/socialmedia/ajaxlogin.php?operation=departments",
            data: {
                "chosenFaculty": chosenFaculty
            },
            dataType: "json",
            success: function(result) {
                $("#MemberDepartment").html(result.departments);
                $("#MemberDepartment").prop("disabled", false);
            }
        });
    });
    $("#RegisterModal").on("click", "#complete_reg", function() {
        var FormID = "form_informations";
        var FormID2 = "form_register";
        var Operation = "completeReg";
        var MemberID = $("#complete_reg").attr("memberid");
        var Datas = $("form#" + FormID).serialize()+"&"+$("form#" + FormID2).serialize();
        $("#spinnerreg").html('<i class="fas fa-spinner fa-spin"></i>');
        $("#complete_reg").prop("disabled", true);
        $.ajax({
            type: "post",
            url: SITE_URL + "/socialmedia/ajaxlogin.php?operation=" + Operation + "&MemberID=" + MemberID,
            data: Datas,
            dataType: "json",
            success: function(result) {
                $("#spinnerreg").html("");
                $("#complete_reg").prop("disabled", false);
                if (result.error) {
                    $("#result2").html("<div class='bg-danger text-white text-center mt-3 mb-1 py-2 rounded-2'>" + "<i class='fas fa-exclamation-triangle'></i> " + result.error + "</div>");
                } else {
                    $("#result2").html("<div class='bg-success text-white text-center mt-3 mb-1 py-2 rounded-2'><i class='fas fa-check-square'></i> " + result.success + "</div>");
                    $("form").trigger("reset");
                    setTimeout(function(){ location.reload(); }, 1000);
                }
            }
        });
    });
</script>