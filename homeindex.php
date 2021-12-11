<script>
  var Part = '<?php
              if (isset($_POST['part'])) {
                echo $_GET["part"];
              } else {
                echo "0";
              };
              ?>';
  $(function() {
    var activeness = 'active';
    $(window).scroll(function() {
      var documentheight = $(document).height();
      var windowheight = $(window).height();
      var differance = (documentheight - windowheight);
      var lastposts_height = $("#posts_container .container:last").height();
      var scrolltop = ($(window).scrollTop() + lastposts_height);
      if (scrolltop > differance && activeness == 'active') {
        activeness = 'inactive';
        var id = $("#posts_container .container:last").attr("id");
        $.ajax({
          type: "post",
          url: "http://localhost/aybu/socialmedia/showposts.php?From=Home",
          data: {
            "id": id,
            "part": Part
          },
          dataType: "json",
          success: function(result) {
            if (result.state == "empty") {} else {
              $("#posts_container").append(result.state);
              activeness = "active";
            }
          }
        });
      }
    });
  });
</script>
<!--BODY PART-->
<div class="d-md-none" id="all-container">
  <!-- MOBİL HABERLER -->
  <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <?php
      $countnews = $db->getColumnData("SELECT COUNT(*) FROM news");
      for ($i = 1; $i < $countnews; $i++) {
      ?>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $i ?>" aria-label="Slide <?= $i ?>"></button>
      <?php } ?>
    </div>
    <div class="carousel-inner">
      <?php
      $news = $db->getDatas("SELECT * FROM news WHERE NewsActiveness = ? ORDER BY NewsOrder ASC", array(1));
      $i = 0;
      foreach ($news as $new) {
      ?>
        <div style="background-image: url('news_images/<?= $new->NewsImg ?>');background-size:cover;background-position:center;background-repeat:no-repeat;" class="carousel-item <?php echo ($i == 0 ? "active" : "") ?>">
          <div class="carousel-caption text-dark rounded-3 p-3">
            <h5><?= $new->NewsHeader ?></h5>
            <p><?= $new->NewsContent ?></p>
            <a href="http://localhost/aybu/socialmedia/<?= $translates["News"] ?>/<?= seolink($new->NewsHeader) . "-" . $new->NewsID ?>" class="btn btn-outline-primary"><?= $translates["viewnews"] ?></a>
          </div>
        </div>
      <?php
        $i += 1;
      } ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
  <!-- MOBİL KULÜP -->
  <ul class="list-group rounded-1 mb-3 mt-5" style="max-height: 40vh;overflow-y:auto;">
    <?php
    $myClubs = $db->getDatas("SELECT * FROM clubmembers WHERE MemberID = ? AND Activeness = ?", array($memberid, 1));
    foreach ($myClubs as $myClub) {
      $Clubinfo = $db->getData("SELECT * FROM clubs WHERE ClubID = ?", array($myClub->ClubID));
      $clubname = $Clubinfo->ClubName;
    ?>
      <a class="list-group-item py-3 text-center bg-transparent fs-5 text-light text-decoration-none topics" href="http://localhost/aybu/socialmedia/<?= $translates["clubs"] ?>/<?= $Clubinfo->ClubID ?>">
        <div class="row">
          <div class="col-2">
            <img src="club_images/<?= $Clubinfo->ClubImg ?>" class="rounded-circle" style="width:50px;height:50px;border:1px solid rgba(255, 255, 255, 0.788);">
          </div>
          <div class="col-10 ps-3 d-flex align-items-center">
            <label class="ps-3" style="cursor:pointer; white-space: nowrap;overflow: hidden;text-overflow: ellipsis;" title="<?= $clubname ?>"><?= $clubname ?></label>
          </div>
        </div>
      </a>
    <?php } ?>
  </ul>
<!-- MOBİL KONULAR -->
  <ul class="list-group rounded-1 mb-3 mt-5" style="max-height: 40vh;overflow-y:auto;">
    <?php
    $topics = $db->getDatas("SELECT * FROM topics WHERE TopicActive = ? ORDER BY TopicOrder ASC", array(1));
    foreach ($topics as $topic) { ?>
      <a class="list-group-item py-3 text-center bg-transparent text-light text-decoration-none topics" href="http://localhost/aybu/socialmedia/<?= $translates["home"] ?>/<?= $topic->TopicLink ?>"><?= $topic->TopicName ?></a>
    <?php } ?>
  </ul>
</div>

<div class="container-fluid p-0 d-md-block">
  <div class="row p-0 m-0">
    <div class="col-2 p-0 m-0 border-end d-none d-md-block" style="height:100%;position:fixed" id="topics_wide">
      <h2 class="text-center p-3 text-light" style="font-size:27px;font-family: 'Nanum Gothic', sans-serif;"><i>Konular</i></h2>
      <ul class="list-group rounded-1 w-100 p-0 m-0" style="height: 31vh;overflow-y:auto;">
        <?php
        $topics = $db->getDatas("SELECT * FROM topics WHERE TopicActive = ? ORDER BY TopicOrder ASC", array(1));
        foreach ($topics as $topic) { ?>
          <a class="list-group-item py-3 text-center bg-transparent fs-5 text-light text-decoration-none topics" href="http://localhost/aybu/socialmedia/<?= $translates["home"] ?>/<?= $topic->TopicLink ?>"><?= $topic->TopicName ?></a>
        <?php } ?>
      </ul>
      <hr class="text-light mt-4">
      <h2 class="text-center p-3 text-light" style="font-size:27px;font-family: 'Nanum Gothic', sans-serif;"><i>Kulüpler</i></h2>
      <ul class="list-group rounded-1 w-100 p-0" style="height: 31vh;overflow-y:auto;">
        <?php
        $myClubs = $db->getDatas("SELECT * FROM clubmembers WHERE MemberID = ? AND Activeness = ?", array($memberid, 1));
        foreach ($myClubs as $myClub) {
          $Clubinfo = $db->getData("SELECT * FROM clubs WHERE ClubID = ?", array($myClub->ClubID));
          $clubname = $Clubinfo->ClubName;
        ?>
          <a class="list-group-item py-3 text-center bg-transparent fs-5 text-light text-decoration-none topics" href="http://localhost/aybu/socialmedia/<?= $translates["clubs"] ?>/<?= $Clubinfo->ClubID ?>">
            <div class="row">
              <div class="col-2">
                <img src="club_images/<?= $Clubinfo->ClubImg ?>" class="rounded-circle" style="width:50px;height:50px;border:1px solid rgba(255, 255, 255, 0.788);">
              </div>
              <div class="col-10 ps-3 d-flex align-items-center">
                <label class="ps-3" style="cursor:pointer; white-space: nowrap;overflow: hidden;text-overflow: ellipsis;" title="<?= $clubname ?>"><?= $clubname ?></label>
              </div>
            </div>
          </a>
        <?php } ?>
      </ul>
    </div>
    <div class="col-md-2"></div>
    <div class="col-12 col-md-8 m-0 px-md-5" id="posts_container">
      <?php require_once "posts_home.php" ?>
    </div>
    <div class="col-12 col-md-8 m-0 px-md-5 loadposts w-100 d-none justify-content-center mb-5"><i class="mx-auto fas fa-circle-notch fa-3x fa-spin text-navbar"></i></div>
    <div class="col-2 offset-md-10 p-0 border-start justify-content-center d-none d-md-block news-section">
      <h2 class="text-center p-4 text-light" style="font-family: 'Nanum Gothic', sans-serif;"><i><?= $translates["news"] ?></i></h2>
      <div class="row justify-content-center">
        <?php
        $news = $db->getDatas("SELECT * FROM news WHERE NewsActiveness = ? ORDER BY NewsOrder ASC", array(1));
        foreach ($news as $new) {
          if (strlen($new->NewsSummarize) > 61) {
            $new->NewsSummarize = substr($new->NewsSummarize, 0, 59);
            $new->NewsSummarize .= "...";
          }
        ?>
          <div class="col-10 text-center">
            <div class="card m-0 mb-4">
              <img src="news_images/<?= $new->NewsImg ?>" class="card-img-top">
              <div class="card-body">
                <h5 class="card-title" style="user-select:text;"><?= $new->NewsHeader ?></h5>
                <p class="card-text text-start" style="user-select:text;font-size:13px;text-align: justify;"><?= $new->NewsSummarize ?></p>
                <a href="http://localhost/aybu/socialmedia/<?= $translates["News"] ?>/<?= seolink($new->NewsHeader) . "-" . $new->NewsID ?>" class="btn btn-primary btn-sm"><?= $translates["viewnews"] ?></a>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<script>
  function openComments(PostID) {
    var state = $("#postcomment_" + PostID).css("display");
    if (state == "none") {
      $("#postcomment_" + PostID).css("display", "flex");
      $("#comments_" + PostID).css("display", "flex");
    } else {
      $("#postcomment_" + PostID).css("display", "none");
      $("#comments_" + PostID).css("display", "none");
    }
  }

  function OpenEditPost(PostID, TextValue) {
    event.preventDefault();
    $("#postmiddle_" + PostID).removeClass("d-flex");
    $("#postmiddle_" + PostID).addClass("d-none");
    $("#likecomment_" + PostID).css("display", "none");
    $("#addpartul_" + PostID).removeClass("d-none");
    $("#addpartul_" + PostID).addClass("d-block");
  }

  function OpenEditComment(CommentID, PostID) {
    event.preventDefault();
    $("#comment_text_" + CommentID).css("display", "none");
    $("#form_editcomment_" + CommentID).removeClass("d-none");
    $("#form_editcomment_" + CommentID).addClass("d-block");
  }

  $(function() {
    $("#image_upload").on("change", function() { //Post Atmada IMAGE
      $("#warn_file").removeClass("d-block");
      $("#warn_file").addClass("d-none");
      $('#posting_img')[0].src = window.URL.createObjectURL(this.files[0]);
      if (window.URL.createObjectURL(this.files[1])) {
        $('#review_more').removeClass("d-none");
        $('#review_more').addClass("d-flex");
        $('#review_more').html('<i class="fas fa-plus"></i>');
      }
      if (window.URL.createObjectURL(this.files[4])) {
        $("#warn_file").removeClass("d-none");
        $("#warn_file").addClass("d-block");
        $("#warn_file").html('<?= $translates["imagelimit"] ?>');
      }
    });
    $("#file_upload").on("change", function() { //Post Atmada FILE
      $("#posting_file").html('');
      let i = 0;
      while (this.files[i]) {
        $("#posting_file").append('<i class="fas fa-file-alt fa-2x"></i> ' + this.files[i]["name"] + '<br><br>');
        i++;
      }
      if (window.URL.createObjectURL(this.files[4])) {
        $("#warn_file").removeClass("d-none");
        $("#warn_file").addClass("d-block");
        $("#warn_file").html('<?= $translates["filelimit"] ?>');
      }
    });

    $("#posts_container").on("change", ".edit_image_upload", function() { //Post editlemede IMAGE
      var PostID = $(this).attr("postid");
      $("#edit_post_images_" + PostID).removeClass("d-flex");
      $("#edit_post_images_" + PostID).addClass("d-none");
      $('#posting_img_edit_' + PostID)[0].src = window.URL.createObjectURL(this.files[0]);
      if (window.URL.createObjectURL(this.files[1])) {
        $('#review_more_edit_' + PostID).removeClass("d-none");
        $('#review_more_edit_' + PostID).addClass("d-flex");
        $('#review_more_edit_' + PostID).html('<i class="fas fa-plus"></i>');
        if (window.URL.createObjectURL(this.files[4])) {
          $("#warn_file_edit_" + PostID).removeClass("d-none");
          $("#warn_file_edit_" + PostID).addClass("d-block");
          $("#warn_file_edit_" + PostID).html('<?= $translates["imagelimit"] ?>');
        }
      }
    });
    $("#posts_container").on("change", ".edit_file_upload", function() { //Post editlemede FILE
      var PostID = $(this).attr("postid");
      $("#edit_post_files_" + PostID).html('');
      let i = 0;
      while (this.files[i]) {
        $("#edit_post_files_" + PostID).append('<div class="col-12 my-2 ps-4 fs-6"><i class="fas fa-file-alt fa-2x text-light"></i> <a class="text-light" href="">' + this.files[i]["name"] + '</a> </div>');
        i++;
      }
      if (window.URL.createObjectURL(this.files[4])) {
        $("#warn_file").removeClass("d-none");
        $("#warn_file").addClass("d-block");
        $("#warn_file").html('<?= $translates["filelimit"] ?>');
      }
    });
  });
</script>