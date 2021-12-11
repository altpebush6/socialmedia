<div class="row">
    <div class="col-12 border bg-light p-4">
        <div class="row">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" href="#Chatboxes">Chatboxes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#Messages">Messages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#Images">Images</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#MemberAbout">MemberAbout</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="row" id="Chatboxes">
                    <?php require_once "chatbox.php"; ?>
                </div>
            </div>
            <div class="col-12" id="Messages">
                <div class="row">
                    <?php require_once "adminmessages.php"; ?>
                </div>
            </div>
            <div class="col-12" id="Images">
                <div class="row">
                    <?php require_once "images.php"; ?>
                </div>
            </div>
            <div class="col-12" id="MemberAbout">
                <div class="row">
                    <?php require_once "memberabout.php"; ?>
                </div>
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