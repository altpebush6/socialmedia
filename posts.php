<div class="row">
    <div class="col-12 border bg-light p-4">
        <div class="row">
            <div class="col-12 col-xxl-6" style="max-height: 100vh;overflow-y:auto;">
                <div class="row justify-content-center fs-3">All Posts</div>
                <div class="row"><?php require_once "posts_content.php"; ?></div>
            </div>
            <div class="col-12 col-xxl-6" style="max-height: 100vh;overflow-y:auto;">
                <div class="row justify-content-center fs-3">Reported Posts</div>
                <div class="row"><?php require_once "reported_posts_content.php"; ?></div>
            </div>
            <div class="col-8 col-xxl-6 mx-auto" style="max-height: 100vh;overflow-y:auto;">
                <div class="row justify-content-center fs-3">Reported Comments</div>
                <div class="row"><?php require_once "reported_comments.php"; ?></div>
            </div>
        </div>
    </div>
</div>

<script>
    function openComments(PostID) {
        var state = $("#comments_" + PostID).css("display");
        if (state == "none") {
            $("#comments_" + PostID).css("display", "flex");
        } else {
            $("#comments_" + PostID).css("display", "none");
        }
    }
    function repopenComments(PostID) {
        var state = $("#repcomments_" + PostID).css("display");
        if (state == "none") {
            $("#repcomments_" + PostID).css("display", "flex");
        } else {
            $("#repcomments_" + PostID).css("display", "none");
        }
    }
    function repComopenComments(PostID) {
        var state = $("#Commentrepcomments_" + PostID).css("display");
        if (state == "none") {
            $("#Commentrepcomments_" + PostID).css("display", "flex");
        } else {
            $("#Commentrepcomments_" + PostID).css("display", "none");
        }
    }
</script>