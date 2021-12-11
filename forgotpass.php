<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "classes/AllClasses.php";

use aybu\session\session as Session;

$part = $_GET["part"];

if (!($part == 'ilkadim' || $part == 'firststep' || $part == 'ikinciadim' || $part == 'secondstep')) {
    header("Location: http://localhost/aybu/socialmedia/404.php");
}

$edit = $_GET["edit"];

if (Session::isHave("Language")) {
    $language = Session::get("Language");
} else {
    $language = "tr";
}
require_once "languages/language_" . $language . ".php";
?>
<html>

<head>
    <title>AYBU | <?= $translates["resetpass"] ?></title>
    <base href="http://localhost/aybu/socialmedia/">
    <link rel="icon" href="images/logo.png" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/913cc5242f.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <link rel="stylesheet" href="css/loginstyle.css">
    <link rel="stylesheet" href="css/bootstrap.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height:90%">
            <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                <?php if ($part == 'ilkadim' or $part == 'firststep') { ?>
                    <div class="card text-center">
                        <div class="card-header fs-5">
                            <?= $translates["resetyourpass"] ?>
                        </div>
                        <div class="card-body">
                            <form id="form_controlemail" method="post">
                                <label for="email" class="form-label text-muted w-100 text-start"><?= $translates["email"] . "*" ?></label>
                                <div class="input-group mb-3">
                                    <input autocomplete="off" type="text" class="form-control" name="email_forgot" id="email_forgot" maxlength="11" placeholder="<?= $translates["yourstudentnumber"] ?>">
                                    <span class="input-group-text">@ybu.edu.tr</span>
                                </div>
                                <div class="" id="result_controlemail"></div>
                        </div>
                        <div class="card-footer text-muted">
                            <button type="button" class="btn btn-secondary w-75" name="controlemail_btn" id="controlemail_btn" onClick="ResetPassword('form_controlemail','sendmail','spinner_controlemail','result_controlemail')"> <?= $translates["continue"] ?> <span class="spinner" id="spinner_controlemail"></span></button>
                        </div>
                        </form>
                    </div>
                <?php } ?>

                <?php if ($part == 'ikinciadim' or $part == 'secondstep') { ?>
                    <div class="card text-center">
                        <div class="card-header fs-5">
                            <?= $translates["resetyourpass"] ?>
                        </div>
                        <div class="card-body">
                            <form id="form_resetpass" method="post">
                                <input type="hidden" name="resetcode" value="<?= $edit ?>">
                                <div class="passwordreg passforgot">
                                    <label for="password_forgot1" class="form-label text-muted w-100 text-start"><?= $translates["Password"] . "*" ?></label>
                                    <div class="input-group mb-3">
                                        <input class="form-control" type="password" name="password_forgot1" id="password_forgot1" maxlength="20" placeholder="<?= $translates["newpasslabel"] ?>">
                                        <span class="input-group-text" style="cursor:pointer;" onClick="changeAttr1()"><i class="fas fa-eye" id="showhide1"></i></span>
                                    </div>
                                    <label for="password_forgot2" class="form-label text-muted w-100 text-start"><?= $translates["passwordagain"] . "*" ?></label>
                                    <div class="input-group mb-3">
                                        <input class="form-control" type="password" name="password_forgot2" id="password_forgot2" maxlength="20" placeholder="<?= $translates["newpassagainlabel"] ?>">
                                        <span class="input-group-text" style="cursor:pointer;" onClick="changeAttr2()"><i class="fas fa-eye" id="showhide2"></i></span>
                                    </div>
                                </div>
                                <div class="bg-success" id="result_resetpass"></div>
                        </div>
                        <div class="card-footer text-muted">
                            <button type="button" class="btn btn-secondary w-75" name="resetpass_btn" id="resetpass_btn" onClick="ResetPassword('form_resetpass','resetpass','spinner_resetpass','result_resetpass')"><?= $translates["save"] ?> <span class="spinner" id="spinner_resetpass"></span></button>
                        </div>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php require_once "functions/ajaxfunctions.php"; ?>
    <script>
        var password1 = document.getElementById("password_forgot1");
        var password2 = document.getElementById("password_forgot2");
        var eyeicon1 = document.getElementById("showhide1");
        var eyeicon2 = document.getElementById("showhide2");

        function changeAttr1() {
            if (password1.type === 'password') {
                password1.type = "text";
                eyeicon1.style.color = "#0085ff"; // Login Şifreyi Gizle/Göster
            } else {
                password1.type = "password";
                eyeicon1.style.color = "black";
            }
        }

        function changeAttr2() {
            if (password2.type === 'password') {
                password2.type = "text";
                eyeicon2.style.color = "#0085ff"; // Login Şifreyi Gizle/Göster
            } else {
                password2.type = "password";
                eyeicon2.style.color = "black";
            }
        }
    </script>
</body>

</html>