<div class="container" id="settings_container">
  <div class="row">
    <div class="col-12">
      <ul class="nav nav-tabs nav-fill nav-justified mt-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link active" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab"><?= $translates["Accountset"] ?></a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab"><?= $translates["Aboutset"] ?></a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" id="resume-tab" data-bs-toggle="tab" data-bs-target="#resume" type="button" role="tab"><?= $translates["resumeset"] ?></a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab"><?= $translates["Passwordset"] ?></a>
        </li>
      </ul>
    </div>
  </div>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active py-3" id="account" role="tabpanel"><?php require_once "partaccount.php" ?></div>
    <div class="tab-pane fade" id="about" role="tabpanel"><?php require_once "partabout.php" ?></div>
    <div class="tab-pane fade" id="resume" role="tabpanel"><?php require_once "partresume.php" ?></div>
    <div class="tab-pane fade py-3" id="password" role="tabpanel"><?php require_once "partpassword.php" ?></div>
  </div>
</div>
<script>
  function EditOpen(EditLink) {
    $("#" + EditLink).addClass("d-none");
    $("#edit_" + EditLink).removeClass("d-none");
    $("#edit_" + EditLink).addClass("d-inline-table");
  }
</script>