<?php
$searchedkey = $part;
require_once "functions/seolink.php";
?>

<div class="container mt-5">
  <div class="row p-2">
    <h2 class="text-center text-light">
      <?php
      if ($language == 'tr') {
        echo $searchedkey . $translates["searchedresult"];
      } else {
        echo $translates["searchedresult"] . $searchedkey;
      }
      ?>
    </h2>
    <small class="text-white mt-3 mt-md-0">
      <?php
      $totalresult = $db->getColumnData("SELECT COUNT(*) FROM members WHERE MemberName LIKE '$searchedkey%'");
      echo $translates["total"] . $totalresult . $translates["foundresult"];
      ?>
    </small>
  </div>
  <div class="row border-top mt-2 justify-content-center">
    <?php
    $members = $db->getDatas("SELECT * FROM members WHERE MemberName LIKE '$searchedkey%'");
    if (count($members) > 5) { ?>
      <div class="col-1 d-flex justify-content-center align-items-center">
        <i class="fas fa-chevron-circle-left text-light fa-2x" id="prevBtn" style="cursor:pointer"></i>
      </div>
    <?php } ?>
    <div class="col-8 col-sm-9 col-md-10 pt-4">
      <div class="owl-carousel owl-theme d-flex justify-content-center" id="containermembers">
        <?php
        foreach ($members as $member) {
          $memberimg = $db->getColumnData("SELECT Member_Profileimg FROM images WHERE MemberID = ?", array($member->MemberID));
          $gender = $db->getColumnData("SELECT MemberGender FROM members WHERE MemberID = ?", array($member->MemberID));
          if (is_null($memberimg)) {
            if ($gender == 'Erkek') {
              $memberimg = "profilemale.png";
            } else {
              $memberimg = "profilefemale.png";
            }
          }
        ?>
 
          <div class="item profile-card profile-card-md my-2 carousel-div text-center friend-box" style="background-image: url('images_profile/<?= $memberimg ?>');">
            <h4 class="text-center p-3 bg-light fs-5 rounded-md-50"><?= $member->MemberName . " " . $member->MemberLastName ?></h4>
            <a class="btn btn-dark rounded-3 mb-5 mb-md-0 mx-auto" href="http://localhost/aybu/socialmedia/<?= $translates['profile'] ?>/<?= $member->MemberID ?>"><?= $translates["goprofile"] ?></a>
          </div>
        <?php } ?>
      </div>
    </div>
    <?php if (count($members) > 5) { ?>
      <div class="col-1 d-flex justify-content-center align-items-center">
        <i class="fas fa-chevron-circle-right text-light fa-2x" id="nextBtn" style="cursor:pointer"></i>
      </div>
    <?php } ?>

  </div>
</div>
<script>
  $(function() {
    $('.owl-carousel').owlCarousel({
      loop: false,
      margin: 10,
      rewind: false,
      center: false,
      autoplay: 5000,
      responsive: {
        0: {
          items: 1
        },
        600: {
          items: 3
        },
        1000: {
          items: 5
        }
      }
    })
    $("#prevBtn").click(function() {
      $("#containermembers").trigger('prev.owl.carousel', [1000]);
    });
    $("#nextBtn").click(function() {
      $("#containermembers").trigger('next.owl.carousel', [1000]);
    });
  });
</script>