<?php

$part = $_GET["part"];
$friend_count = $db->getColumnData("SELECT COUNT(*) FROM friends WHERE (FirstMemberID = ? OR SecondMemberID = ?) AND FriendRequest = ?", array($memberid, $memberid, 1));
if ($part) {
  $cover_photo = $db->getColumnData("SELECT Member_Coverimg FROM images WHERE MemberID = ?", array($part));
  $profile_photo = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($part));
  $user_name = $db->getColumnData("SELECT MemberName FROM members WHERE MemberID = ? ", array($part));
  $user_lastname = $db->getColumnData("SELECT MemberLastName FROM members WHERE MemberID = ?", array($part));
  $friend_count = $db->getColumnData("SELECT COUNT(*) FROM friends WHERE (FirstMemberID = ? OR SecondMemberID = ?) AND FriendRequest = ?", array($part, $part, 1));
  $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($part));
  if (is_null($cover_photo)) {
    $cover_photo = "noncover.png";
  }
  if (is_null($profile_photo)) {
    if ($gender == 'Erkek') {
      $profile_photo = "profilemale.png";
    } else {
      $profile_photo = "profilefemale.png";
    }
  }
}
?>
<script>
  var Part = '<?php
              if ($part) {
                echo $part;
              } else {
                echo "0";
              }
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
          url: "http://localhost/aybu/socialmedia/showposts.php?From=Profile",
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
<div class="container px-md-5">
  <div class="row">
    <div class="col-12 col-md-12 p-0 d-flex justify-content-end" id="user-coverphoto">
      <a class="w-100" href="images_cover/<?= $cover_photo ?>">
        <img class="w-100 h-40 h-md-45" src="images_cover/<?= $cover_photo ?>">
      </a>
      <?php if (!$part or ($memberid == $part)) { ?>
        <button class="align-self-end text-decoration-none btn-sm bg-primary p-2 mb-2 me-2 rounded-3 text-light" style="position:absolute;border:none;" data-bs-toggle="modal" data-bs-target="#CoverPhotoModal">
          <i class="fas fa-image"></i> <label style="cursor: pointer;"><?= $translates["addcoverphoto"] ?></label>
        </button>
      <?php } ?>
    </div>
  </div>
  <div class="row justify-content-center align-self-end mt--20 mt-md--12 mt-xl--10 mb-3" id="user-profilephoto">
    <div class="col-4 d-flex justify-content-center">
      <div class="d-flex justify-content-end align-items-end">
        <a href="images_profile/<?= $profile_photo ?>">
          <img title="<?= $user_name . " " . $user_lastname ?>" src="images_profile/<?= $profile_photo ?>" class='rounded-circle' width="140" height="140">
        </a>
        <?php if (!$part or ($memberid == $part)) { ?>
          <i data-bs-toggle="modal" data-bs-target="#ProfilePhotoModal" class="fas fa-camera position-absolute fs-4 text-dark mb-2 me-2" style="cursor:pointer;" title="<?= $translates["changeyourprofilephoto"] ?>"></i>
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="row justify-content-center">
    <div class="col-3 text-center text-light py-2 fs-4 bg-navbar-name" style="border-top-left-radius:20px !important;border-top-right-radius:20px !important;"><?= $user_name . " " . $user_lastname ?></div>
  </div>
  <div class="row justify-content-center bg-navbar" style="border-radius: 10px;">
    <div class="col-4 text-center p-3 fs-5 text-light d-flex align-items-center justify-content-center" style="border-right:1px solid gray;">
      <?php if (!$part or $memberid == $part) { ?>
        <a href="javascript:void(0)" class="text-decoration-none text-light" data-bs-toggle="modal" data-bs-target="#bioModal"><?= $translates["editbio"] ?></a>
      <?php } else { ?>
        <a class="text-decoration-none text-light" href="http://localhost/aybu/socialmedia/<?= $translates["messages"] ?>/<?= $part ?>"><i class="fab fa-facebook-messenger me-1"></i> Mesaj</a>
      <?php } ?>
    </div>
    <div class="col-4 text-center p-3 fs-4 text-light d-flex align-items-center justify-content-center">
      <?php if (!$part or $memberid == $part) { ?>
        <span class="text-center text-light"><?= $translates["yourprofile"] ?></span>
      <?php } else { ?>
        <div class="friend-request" id="friend-request">
          <?php
          $isfriend = $db->getData("SELECT * FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ? AND FriendRequest = ?", array($memberid, $part, 1));
          $isfriend2 = $db->getData("SELECT * FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ? AND FriendRequest = ?", array($part, $memberid, 1));
          $sentrequest = $db->getData("SELECT * FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ? AND FriendRequest = ?", array($memberid, $part, 0));
          $hasrequest = $db->getData("SELECT * FROM friends WHERE FirstMemberID = ? AND SecondMemberID = ? AND FriendRequest = ?", array($part, $memberid, 0));
          ?>
          <?php if ($sentrequest) { ?>
            <button class="btn btn-outline-light" id="SentFriendButton" onClick="FriendButton('remove','<?= $part ?>')"><i class='fas fa-user-check'></i> <?= $translates["friendrequestsent"] ?></button>
          <?php } elseif ($hasrequest) { ?>
            <button class="btn btn-outline-light" id="RequestFriendButton" onClick="FriendButton('requestAccept','<?= $part ?>')"><i class='fas fa-user-plus'></i> <?= $translates["admitrequest"] ?></button>
          <?php } elseif ($isfriend or $isfriend2) { ?>
            <button class="btn btn-outline-light" id="RemoveFriendButton" onClick="FriendButton('remove','<?= $part ?>')"><i class='fas fa-user-check'></i> <?= $translates["youarefriend"] ?></button>
          <?php } else { ?>
            <button class="btn btn-outline-light" id="addFriendButton" onClick="FriendButton('add','<?= $part ?>')"><i class="fas fa-user-plus"></i> <?= $translates["addfriend"] ?></button>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
    <div class="col-4 p-3 fs-5 text-light d-flex align-items-center justify-content-center" style="border-left:1px solid gray;">
      <?php if (!$part or ($memberid == $part)) { ?>
        <a href="http://localhost/aybu/socialmedia/<?= $translates["friends"] ?>" class="text-center text-decoration-none text-light"><?= $translates["friendstitle"] . "(" . $friend_count . ")" ?></a>
      <?php } else { ?>
        <a href="http://localhost/aybu/socialmedia/<?= $translates["friends"] ?>/<?= $part ?>" class="text-center text-decoration-none text-light"><?= $translates["friendstitle"] . "(" . $friend_count . ")" ?></a>
      <?php } ?>
    </div>
  </div>
  <?php
  if ($part) {
    $memberBio = $db->getColumnData("SELECT Biography FROM memberbiography WHERE MemberID = ?", array($part));
  } else {
    $memberBio = $db->getColumnData("SELECT Biography FROM memberbiography WHERE MemberID = ?", array($memberid));
  }
  if ($memberBio) { ?>

    <div class="row justify-content-center">
      <div class="col-3 text-center text-light py-2 fs-4 bg-navbar-name" style="border-bottom-left-radius:20px !important;border-bottom-right-radius:20px !important;">
        <span class="fs-6"><?= $memberBio ?></span>
      </div>
    </div>
  <?php } ?>
  <?php
  if (($isfriend or $isfriend2) or !$part or ($memberid == $part)) {
    if ($part) {
      $memberClubs = $db->getDatas("SELECT * FROM clubmembers WHERE MemberID = ? AND Activeness = ?", array($part, 1));
    } else {
      $memberClubs = $db->getDatas("SELECT * FROM clubmembers WHERE MemberID = ? AND Activeness = ?", array($memberid, 1));
    }
    if ($memberClubs) {
  ?>
      <div class="container mt-4">
        <div class="row">
          <div class="col-12 owl-carousel owl-theme d-flex justify-content-center " id="containerclubs">
            <?php foreach ($memberClubs as $club) {
              $clubinfos = $db->getData("SELECT * FROM clubs WHERE ClubID = ? AND ClubState = ?", array($club->ClubID, 1));
            ?>
              <a class="text-light" href="http://localhost/aybu/socialmedia/<?= $translates["clubs"] ?>/<?= $club->ClubID ?>">
                <div class="border item carousel-div d-flex justify-content-center align-items-center mx-3 rounded-circle" style="width:85px;height:85px;background-image: url('club_images/<?= $clubinfos->ClubImg ?>');"></div>
              </a>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php }
    if ($part) {
      $memberEvents = $db->getDatas("SELECT * FROM eventparticipants WHERE MemberID = ?", array($part));
    } else {
      $memberEvents = $db->getDatas("SELECT * FROM eventparticipants WHERE MemberID = ?", array($memberid));
    }
    if ($memberEvents) { ?>
      <div class="container my-5">
        <div class="row">
          <div class="col-12 mb-2" style="border-bottom: 1px solid rgba(46, 46, 46, 0.2);">
            <h2 class="header text-light text-center" style="font-family: 'Libre Baskerville', serif;"><?= $translates["joinedevents"] ?></h2>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="mt-4 ps-events owl-carousel owl-theme d-flex justify-content-center" id="containerevents">
              <?php foreach ($memberEvents as $eachEvent) {
                $event = $db->getData("SELECT * FROM events WHERE EventID = ?", array($eachEvent->EventID)); ?>
                <div class="pe-5">
                  <div class="row border rounded-3 each-event" style="width:500px;height:26vh;overflow:hidden;">
                    <div class="col-5 m-0 p-0">
                      <img src="events_images/<?= $event->EventImage ?>" class="w-100 rounded-3" style="height:100%">
                    </div>
                    <div class="col-7 view-event justify-content-center align-items-center" style="display: none;">
                      <a class="btn btn-outline-light" href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>/<?= seolink($event->EventHeader) . "-" . $event->EventID ?>"><?= $translates["viewevent"] ?></a>
                    </div>
                    <div class="col-7 py-2 event-infos">
                      <div style="height:19vh;">
                        <div class="col-12 p-0">
                          <h4><?= $event->EventHeader ?></h4>
                        </div>
                        <div class="col-12 p-0 cuttheline">~<?= $db->getColumnData("SELECT UniversityName FROM universities WHERE UniversityID = ?", array($event->EventSchool)) ?></div>
                        <div class="col-12 p-0 cuttheline">~<?= $db->getColumnData("SELECT CityName FROM cities WHERE CityID = ?", array($event->EventCity)) ?></div>
                        <div class="col-12 p-0 cuttheline">~<?= $event->EventDateTime ?></div>
                        <div class="col-12 p-0 cuttheline">~<?= $event->EventParticipant ?> <?= $translates["participant"] ?></div>
                      </div>
                      <div class="p-0 fs-3 text-end text-light" style="height:2vh;">
                        <?= ($event->EventPrice == 0 ? $translates['free'] : $event->EventPrice . "₺") ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
    <div class="container my-4">
      <div class="row">
        <div class="col-12" style="border-bottom: 1px solid rgba(46, 46, 46, 0.2);">
          <div class="row justify-content-end">
            <div class="col-12 mb-2">
              <h2 class="header text-light text-center" style="font-family: 'Libre Baskerville', serif;"><?= $translates["courses"] ?></h2>
            </div>
            <?php
            if ($memberid == $part || !$part) { ?>
              <div class="col-3 pt-2 position-absolute text-end">
                <button class="btn btn-dark  w-50" data-bs-toggle="modal" data-bs-target="#courseModal"><i class="fas fa-plus"></i> <?= $translates["addcourse"] ?></button>
              </div>
            <?php } ?>
          </div>
        </div>
        <div class="col-12 mt-2">
          <div class="row justify-content-center">
            <?php
            if ($part) {
              $memberCourses = $db->getDatas("SELECT * FROM membercourses WHERE MemberID = ?", array($part));
            } else {
              $memberCourses = $db->getDatas("SELECT * FROM membercourses WHERE MemberID = ?", array($memberid));
            }
            if ($memberCourses) {
              foreach ($memberCourses as $membercourse) {
                $course = $db->getData("SELECT * FROM courses WHERE CourseID = ?", array($membercourse->CourseID));
            ?>
                <div class="col-2 text-center btn btn-lg btn-success fs-5 me-1 mb-1" title="<?= $course->CourseName ?>">
                  <?= $course->CourseCode ?>
                </div>
              <?php }
            } else { ?>
              <div class="col-10 mx-auto my-3 p-4 text-light fs-5 text-center rounded-3 border"><?= $translates["nocourse"] ?></div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <div class="container mt-4">
      <div class="row">
        <div class="col-6" style="border-right: 1px solid rgba(46, 46, 46, 0.2);">
          <div class="row justify-content-end profile-about">
            <h2 class="header text-light text-center" style="font-family: 'Libre Baskerville', serif;"><?= $translates["About"] ?></h2>
            <?php if (!$part or $memberid == $part) { ?>
              <div class="col-2 align-self-start position-absolute text-end about-edit">
                <a class="text-light" href="http://localhost/aybu/socialmedia/<?= $translates["settings"] ?>"><i class="far fa-edit"></i></a>
              </div>
            <?php } ?>
          </div>
        </div>
        <div class="col-6">
          <div class="row justify-content-end profile-cv">
            <h2 class="header text-light text-center" style="font-family: 'Libre Baskerville', serif;"><?= $translates["resume"] ?></h2>
            <?php if (!$part or $memberid == $part) { ?>
              <div class="col-2 align-self-start position-absolute text-end cv-edit">
                <a class="text-light" href="http://localhost/aybu/socialmedia/<?= $translates["settings"] ?>"><i class="far fa-edit"></i></a>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-6">
          <ul class="list-group rounded-1 mb-3">
            <?php
            if ($part) {
              $person_abouts = $db->getData("SELECT * FROM memberabout WHERE MemberID = ?", array($part));
            } else {
              $person_abouts = $db->getData("SELECT * FROM memberabout WHERE MemberID = ?", array($memberid));
            }
            foreach ($person_abouts as $key => $value) {
              if ($value != "" && $key != "MemberID" && $key != "AboutID") {
                switch ($key) {
                  case 'MemberPhone':
                    $value = "(0" . substr($value, 0, 3) . ")-" . substr($value, 3, 3) . "-" . substr($value, 6, 4);
                    break;
                  case 'MemberBirthday':
                    $value = explode("-", $value);
                    $value = $value[2] . " " . getmonth($value[1]) . " " . $value[0];
                    break;
                  case 'MemberUniversity':
                    $value = $db->getColumnData("SELECT UniversityName FROM universities WHERE UniversityID = ?", array($value));
                    break;
                  case 'MemberFaculty':
                    $value = $db->getColumnData("SELECT FacultyName FROM faculties_$language WHERE FacultyID = ?", array($value));
                    break;
                  case 'MemberDepartment':
                    $value = $db->getColumnData("SELECT DepartmentName FROM departments_$language WHERE DepartmentID = ?", array($value));
                    break;
                  case 'MemberCountry':
                    $value = $db->getColumnData("SELECT CountryName FROM countries WHERE CountryID = ?", array($value));
                    break;
                  case 'MemberHometown':
                  case 'MemberCity':
                    $value = $db->getColumnData("SELECT CityName FROM cities WHERE CityID = ?", array($value));
                    break;
                }

            ?>
                <a class="list-group-item text-center bg-transparent text-light p-3"><span class="fw-bold"><?= $translates[$key] ?>: </span><?= $value ?></a>
            <?php }
            } ?>
          </ul>
        </div>
        <div class="col-6">
          <ul class="list-group rounded-1 mb-3">
            <?php
            if ($part) {
              $person_resume = $db->getData("SELECT * FROM memberresume WHERE MemberID = ?", array($part));
            } else {
              $person_resume = $db->getData("SELECT * FROM memberresume WHERE MemberID = ?", array($memberid));
            }
            foreach ($person_resume as $key => $value) {
              if ($value && $key != "MemberID" && $key != "ResumeID") { ?>
                <a class="list-group-item text-center bg-transparent text-light p-3"><span class="fw-bold"><?= $translates[$key] ?>: </span><?= $value ?></a>
            <?php }
            } ?>
          </ul>
        </div>
      </div>

    </div>

    <div class="row my-4 d-flex justify-content-center" id="posts_container">
      <h2 class="header text-light text-center border-bottom pb-3 w-50" style="font-family: 'Libre Baskerville', serif;"><?= $translates["posts"] ?></h2>
      <?php require_once "posts_profile.php"; ?>
    </div>
  <?php } elseif ($sentrequest) { ?>
    <div class="row my-3 text-center text-dark fs-3" id="doknow">
      <h2><?php echo ($language == 'en' ? $translates["requesthassent"] . $user_name : $user_name . $translates["requesthassent"]) ?></h2>
      <p><?= $translates["waitrequest"] ?></p>
      <button class="btn btn-outline-light w-75 mx-auto" id="SentFriendButton" onClick="FriendButton('remove','<?= $part ?>')"><i class="fas fa-user-plus"></i> <?= $translates["friendrequestsent"] ?></button>
    </div>
  <?php } elseif ($hasrequest) { ?>
    <div class="row my-3 text-center text-dark fs-3" id="doknow">
      <h2><?php echo ($language == 'en' ? $translates["doknow"] . $user_name : $user_name . $translates["doknow"]) . "?" ?></h2>
      <p><?= $translates["admitrequestp"] ?></p>
      <button class="btn btn-outline-light w-75 mx-auto" id="RequestFriendButton" onClick="FriendButton('requestAccept','<?= $part ?>')"><i class="fas fa-user-plus"></i> <?= $translates["admitrequest"] ?></button>
    </div>
  <?php } elseif (!$isfriend2 and !$isfriend) { ?>
    <div class="row my-3 text-center text-dark fs-3" id="doknow">
      <h2><?php echo ($language == 'en' ? $translates["doknow"] . $user_name : $user_name . $translates["doknow"]) . "?" ?></h2>
      <p><?= $translates["sendrequestp"] ?></p>
      <button class="btn btn-outline-light w-75 mx-auto" id="addFriendButton" onClick="FriendButton('add','<?= $part ?>')"><i class="fas fa-user-plus"></i> <?= $translates["addfriend"] ?></button>
    </div>
  <?php } ?>
</div>

<div class="modal fade" id="CoverPhotoModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h5 class="modal-title" id="exampleModalLabel"><?= $translates["createcoverphoto"] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pb-0 px-4">
        <form method="post" id="form_cover" autocomplete="off" enctype="multipart/form-data">
          <div class="row mb-3">
            <label for="coverphoto-upload" class="btn btn-primary"><?= $translates["choosephoto"] ?></label>
            <input type="file" name="image1" id="coverphoto-upload" class="d-none">
          </div>
          <div class="row mb-3">
            <button type="submit" name="uploadimg1" class="btn btn-success"><?= $translates["save"] ?> <span class="spinner" id="spinnerimg1"></span></button>
          </div>
        </form>
      </div>
      <div class="modal-footer text-center px-2">
        <form class="w-100" action="pr_img.php" method="post">
          <button type="submit" name="deleteimg1" class="btn btn-danger w-100"><?= $translates["deletephoto"] ?></button>
        </form>
      </div>
      <hr id="result_cvr_hr" style="display:none">
      <div id="result_cvr_img" class="bg-danger text-light mx-auto mb-3 text-center rounded-3" style="display:none;"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="ProfilePhotoModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h5 class="modal-title" id="exampleModalLabel"><?= $translates["createprofilephoto"] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pb-0 px-4">
        <div id="uploadimageModal" class="d-none justify-content-center">
          <div class="row">
            <div class="col-md-8 text-center">
              <div id="image_demo" style="width:350px;margin-top:30px;"></div>
            </div>
          </div>
        </div>
        <!-- <div class="row mb-4 d-none" id="image_review">
          <div class="col-6 mx-auto p-0 d-flex justify-content-end" id="image_demo">
            <img id="profile_image" src="" class="w-100">
          </div>
        </div> -->
        <form method="post" id="form_profile" autocomplete="off" enctype="multipart/form-data">
          <div class="row mb-3">
            <label for="upload_image" class="btn btn-primary"><?= $translates["choosephoto"] ?></label>
            <input type="file" name="upload_image" id="upload_image" class="d-none">
          </div>
          <div class="row mb-3">
            <button type="button" name="uploadimg2" class="btn btn-success crop-image"><?= $translates["save"] ?> <span class="spinner" id="spinnerimg1"></span></button>
          </div>
        </form>
      </div>
      <div class="modal-footer text-center px-2">
        <form class="w-100" action="pr_img.php" method="post">
          <button type="submit" name="deleteimg2" class="btn btn-danger w-100"><?= $translates["deletephoto"] ?></button>
        </form>
      </div>
      <hr id="result_pr_hr" style="display:none">
      <div id="result_pr_img" class="bg-danger text-light mx-auto mb-3 text-center rounded-3" style="display:none;"></div>
    </div>
  </div>
</div>

<!-- KURS EKLE -->
<div class="modal fade" id="courseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?= $translates["addcourse"] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="searchCourse" placeholder="Kurs adını giriniz" class="form-control mb-3">
        <div class="container">
          <div class="row ps-1">
            <div class="col-7 offset-1"><b><?= $translates["coursename"] ?></b></div>
            <div class="col-4"><b><?= $translates["coursecode"] ?></b></div>
          </div>
        </div>
        <div class="container" style="height: 40vh;overflow:auto" id="allCourses">
          <?php $allcourses = $db->getDatas("SELECT * FROM courses ORDER BY CourseName ASC");
          foreach ($allcourses as $course) {
            $ishaveCourse = $db->getData("SELECT * FROM membercourses WHERE MemberID = ? AND CourseID = ?", array($memberid, $course->CourseID)); ?>
            <div class="row my-2">
              <div class="col-1 text-end m-0 p-0"><input type="checkbox" id="selectCourse_<?= $course->CourseID ?>" courseid="<?= $course->CourseID ?>" class="form-check-input selectCourse" style="cursor: pointer;" <?= ($ishaveCourse) ? "checked" : " " ?>></div>
              <div class="col-11">
                <label for="selectCourse_<?= $course->CourseID ?>" class="w-100" style="cursor: pointer;">
                  <div class="row">
                    <div class="col-8 ps-3 border-end"><?= $course->CourseName ?></div>
                    <div class="col-4"><?= $course->CourseCode ?></div>
                  </div>
                </label>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="submitCourses"><?= $translates["save"] ?></button>
      </div>
    </div>
  </div>
</div>
<!-- BIYOGRAFI -->
<div class="modal fade" id="bioModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bioLabel"><?= $translates["editbio"] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" id="form_bio" autocomplete="off">
          <div class="form-floating">
            <textarea class="form-control" placeholder="Leave a comment here" id="biography" name="biography" style="height: 100px"><?= $memberBio ?></textarea>
            <label for="biography"><?= $translates["bio"] ?></label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="submitBio"><?= $translates["save"] ?> <span id="spinnerbio"></span></button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(function() {
    $('.owl-carousel').owlCarousel({
      loop: false,
      rewind: false,
      center: false,
      autoWidth: true,
      autoplay: 5000,
      stagePadding: 0,
      items: 1,
    })
  });

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
  baguetteBox.run('#user-profilephoto');
  baguetteBox.run('#user-coverphoto');

  $(function() {

    $("#image_upload").on("change", function() { //Post Atmada IMG
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