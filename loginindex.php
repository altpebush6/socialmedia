<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "classes/AllClasses.php";

use aybu\session\session as Session;

$db = new aybu\db\mysqlDB();

if (Session::isHave("Language")) {
  $language = $SS->get("Language");
} else {
  $language = "tr";
}
require_once "languages/language_" . $language . ".php";


?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title><?= $translates["loginsignup"] ?>| AYBU</title>
  <base href="http://localhost/aybu/socialmedia/">
  <link rel="icon" href="images/login_logo.png" type="image/x-icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="css/loginstyle.css" rel="stylesheet" type="text/css" />
  <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
  <script src="https://kit.fontawesome.com/913cc5242f.js" crossorigin="anonymous"></script>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&family=Roboto:wght@300&family=RocknRoll+One&display=swap" rel="stylesheet">
  <!-- GOOGLE FONTS -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Glory:wght@100&family=Kaisei+Tokumin:wght@500&display=swap" rel="stylesheet">
  <!-- GOOGLE FONTS -->
</head>

<body>
  <div class="container pt-5" style="height:90vh;">
    <div class="row align-items-center justify-content-around" style="height:90vh;">
      <div class="col-9 col-md-5 col-lg-4 col-xl-4 col-xxl-3 bg-light rounded-3 py-4 shadow">
        <form method="post" id="form_login" class="login">
          <h2 class="form-label text-center mb-3 mb-xl-4" style="font-family: 'Kaisei Tokumin', serif;">AYBU</h2>
          <input class="form-control" type="text" name="getemail" id="getemail" maxlength="50" placeholder="<?= $translates["youremail"] ?>">
          <div class="input-group mt-3 mt-xl-4">
            <input class="form-control" type="password" name="getpassword" id="getpassword" maxlength="20" placeholder="<?= $translates["pass"] ?>" class="disableit">
            <span class="input-group-text" style="cursor:pointer;" onClick="changeAttr_login()"><i class="fas fa-eye" id="showhide_login"></i></span>
          </div>
          <p id="result1" class="mt-xl-3"></p>
          <button type="button" name="girissubmit" id="girissubmit" class="shadow-sm btn btn-primary w-100 mt-xl-4 mt-xxl-2" onClick="SendFormLog('form_login','login','http://localhost/aybu/socialmedia/index.php')"><?= $translates["logintext"] ?> <span class="spinner" id="spinnerlog"></span></button>
          <div class="row mt-3 my-xl-4">
            <div class="col-12 text-center">
              <a class="link-primary" href="http://localhost/aybu/socialmedia/<?= $translates["forgotpassword"] ?>/<?= $translates["firststep"] ?>"><?= $translates["forgotpass"] ?></a>
              <span>|</span>
              <span class="language-flag">
                <a href="javascript:void(0)"><img class="rounded-3 shadow-sm" style="width:35px;height:20px;" src="images/tr.png" onClick="ChangeLang('tr','<?= $page ?>')" <?php echo ($language == 'tr' ? 'style="opacity:1"' : 'style="opacity:0.7"') ?>></a>
                <a href="javascript:void(0)"><img class="rounded-3 shadow-sm" style="width:35px;height:20px;" src="images/en.png" onClick="ChangeLang('en','<?= $page ?>')" <?php echo ($language == 'en' ? 'style="opacity:1"' : 'style="opacity:0.7"') ?>></a>
              </span>
            </div>
          </div>
          <hr>
          <button type="button" class="shadow-sm btn btn-success w-100 my-xl-2 my-xxl-0" data-bs-toggle="modal" data-bs-target="#RegisterModal"><?= $translates["createaccount"] ?></button>
        </form>
      </div>
      <div class="col-12 col-md-6 col-xl-6 col-xxl-6 mx-md-4">
        <div class="row justify-content-center">
          <div class="col-6 col-md-8 col-xl-6 col-xxl-5 text-center mt-5">
            <img src="images/login_logo.png" class="w-100">
          </div>
          <div class="col-10 col-md-12 col-xl-10 mt-3 text-center fs-3" style="color:#5a49e3;font-family: 'Glory', sans-serif;font-weight:bold;">
            <p><?= $translates["titlelogin"] ?></p>
            <p><?= $translates["programmer"] ?>: Ebubekir Alatepe</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="RegisterModal" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><?= $translates["createregistration"] ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pb-0" id="registration">
          <div class="firstpart">
            <!-- FIRST PART -->
            <form method="post" id="form_register" autocomplete="off">
              <div class="row mb-3">
                <div class="col-5">
                  <label for="name" class="form-label text-muted"><?= $translates["name"] . "*" ?></label>
                  <input class="form-control" type="text" name="name" id="name" maxlength="20" placeholder="<?= $translates["yourname"] ?>">
                </div>
                <div class="col-7">
                  <label for="lastname" class="form-label text-muted"><?= $translates["lastname"] . "*" ?></label>
                  <input class="form-control" type="text" name="lastname" id="lastname" maxlength="20" placeholder="<?= $translates["yourlastname"] ?>">
                </div>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label text-muted"><?= $translates["email"] . "*" ?></label>
                <div class="input-group">
                  <input type="text" class="form-control" name="email" id="email" maxlength="11" placeholder="<?= $translates["yourstudentnumber"] ?>">
                  <span class="input-group-text">@ybu.edu.tr</span>
                </div>
                <div id="email" class="form-text"><?= $translates["emailinfo"] ?></div>
              </div>
              <label for="getpassword" class="form-label text-muted"><?= $translates["Password"] . "*" ?></label>
              <div class="mb-3">
                <div class="input-group">
                  <input class="form-control" type="password" name="password" id="password_reg" maxlength="20" placeholder="<?= $translates["newpass"] ?>">
                  <span class="input-group-text" style="cursor:pointer;" onClick="changeAttr_reg()"><i class="fas fa-eye" id="showhide_reg"></i></span>
                </div>
                <div id="password_reg" class="form-text"><?= $translates["passwordinfo"] ?></div>
              </div>
              <label for="gender" class="form-label text-muted"><?= $translates["gender"] . "*" ?></label>
              <div class="mb-3">
                <input class="btn-check" type="radio" name="gender" id="genderman" value="Erkek" checked>
                <label class="btn btn-outline-primary" for="genderman"><?= $translates["man"] ?></label>
                <input class="btn-check" type="radio" name="gender" id="genderwoman" value="Kadın">
                <label class="btn btn-outline-danger" for="genderwoman"><?= $translates["woman"] ?></label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="contract" id="contract">
                <label class="form-check-label" for="contract"><?= $translates["contract"] . "*" ?></label>
              </div>
            </form>
          </div>
          <!-- NEXT PART -->
          <div class="row d-none" id="nextpart">
            <form method="post" id="form_informations" autocomplete="off">
              <div class="col-12 my-2">
                <label for="name" class="form-label text-muted"><?= $translates["school"] . "*" ?></label>
                <select name="MemberSchool" id="MemberSchool" class="form-select w-100 mx-auto">
                  <option value="0" disabled selected><?= $translates["chooseauni"] ?></option>
                  <?php
                  $unis = $db->getDatas("SELECT * FROM universities");
                  foreach ($unis as $uni) {
                  ?>
                    <option value="<?= $uni->UniversityID; ?>"><?= $uni->UniversityName; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-12 my-2">
                <label for="name" class="form-label text-muted"><?= $translates["faculty"] . "*" ?></label>
                <select name="MemberFaculty" id="MemberFaculty" class="form-select w-100 mx-auto">
                  <option value="0" disabled selected><?= $translates["chooseafaculty"] ?></option>
                  <?php
                  $faculties = $db->getDatas("SELECT * FROM faculties_$language");
                  foreach ($faculties as $faculty) {
                  ?>
                    <option value="<?= $faculty->FacultyID; ?>"><?= $faculty->FacultyName; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-12 my-2">
                <label for="name" class="form-label text-muted"><?= $translates["department"] . "*" ?></label>
                <select name="MemberDepartment" id="MemberDepartment" class="form-select w-100 mx-auto" disabled>
                  <option value="0" disabled selected><?= $translates["firstchoosefaculty"] ?></option>
                </select>
              </div>
              <div class="col-12 my-2">
                <label for="contact" class="form-label text-muted"><?= $translates["phone"] ?></label>
                <div class="input-group">
                  <div class="input-group-text">
                    <img src="images/tr.png" class="rounded-1" style="height:2vh;">
                    <span class="ps-1" style="height: 3.6vh;">+90</span>
                  </div>
                  <input type="text" name="contact" id="contact" class="form-control" maxlength="11" placeholder="<?= $translates["phone"] ?>">
                </div>
              </div>
              <div class="col-12 my-2">
                <label for="birthday" class="form-label text-muted"><?= $translates["birthday"] . "*" ?></label>
                <input class="form-control" type="date" name="birthday" id="birthday" value="2002-12-02">
              </div>
            </form>
          </div>
          <p id="result2"></p>
        </div>
        <div class="modal-footer text-center">
          <button type="button" memberid="" class="shadow-sm btn btn-success w-100" id="continue_reg"> <?= $translates["continue"] ?> <span class="spinner" id="spinnerreg"></span></button>
        </div>
      </div>
    </div>
  </div>

  <?php require_once "functions/ajaxloginfunctions.php"; ?>

  <script>
    var password_login = document.getElementById("getpassword");
    var password_reg = document.getElementById("password_reg");
    var eyeicon_login = document.getElementById("showhide_login");
    var eyeicon_reg = document.getElementById("showhide_reg");

    var registerpart = document.getElementById("registerpart");
    var registercontainer = document.getElementById("registercontainer");
    var container = document.getElementById("container");

    var passwordinput = document.getElementById("getpassword");

    passwordinput.addEventListener("keyup", function(event) {
      if (event.keyCode === 13) {
        event.preventDefault();
        document.getElementById("girissubmit").click();
      }
    })

    function changeAttr_login() {

      if (password_login.type === 'password') {
        password_login.type = "text";
        eyeicon_login.style.color = "#0085ff"; // Login Şifreyi Gizle/Göster
      } else {
        password_login.type = "password";
        eyeicon_login.style.color = "black";
      }
    }

    function changeAttr_reg() {

      if (password_reg.type === 'password') {
        password_reg.type = "text";
        eyeicon_reg.style.color = "#0085ff"; // Register Şifreyi Gizle/Göster
      } else {
        password_reg.type = "password";
        eyeicon_reg.style.color = "black";
      }
    }
  </script>
</body>

</html>