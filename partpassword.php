<div class="container bg-light py-2 rounded-3 shadow">
  <div class="row my-3 justify-content-center">
    <div class="col-12">
      <h3 class="border-bottom pb-4 text-center"><?=$translates["Passwordset"]?></h3>
      <form method="post" id="form_pass">
        <div class="row mb-4 justify-content-center">
          <label for="pass_old" class="col-4 col-md-3 form-label"><?= $translates["passlabel"] ?></label>
          <div class="col-8">
            <input class="form-control shadow" type="password" placeholder="<?= $translates["pass"] ?>" name="pass_old" id="pass_old">
          </div>
        </div>

        <div class="row my-4 justify-content-center">
          <label for="pass_new" class="col-4 col-md-3 form-label"><?= $translates["newpasslabel"] ?></label>
          <div class="col-8">
            <input class="form-control shadow" type="password" placeholder="<?= $translates["newpass"] ?>" name="pass_new" id="pass_new">
          </div>
        </div>

        <div class="row my-4 justify-content-center">
          <label for="pass_new_again" class="col-4 col-md-3 form-label"><?= $translates["newpassagainlabel"] ?></label>
          <div class="col-8">
            <input class="form-control shadow" type="password" placeholder="<?= $translates["newpassagain"] ?>" name="pass_new_again" id="pass_new_again">
          </div>
        </div>

        <div id="pass-result" class="shadow"></div>

        <div class="row justify-content-center">
          <button type="button" class="btn btn-post shadow w-50" name="submitpassword" id="submitpassword" onClick="SendFormPass('form_pass','change_pass','/socialmedia/index.php?page=settings&part=password')"><?= $translates["changepass"] ?>
            <span class="spinner" id="pass_spinner"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>