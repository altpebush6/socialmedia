<div class="row mt-4" id="abouts_container">
  <div class="col-12">
    <table class="table table-light table-hover shadow">
      <thead>
        <th></th>
        <th class="fs-4 p-3 text-center"><?= $translates["Aboutset"] ?></th>
        <th></th>
      </thead>
      <?php
      $list = $db->getDatas("SELECT * FROM nav_about_$language");
      foreach ($list as $items) {
        $getItem = $items->NavDB;
        $contents_about = $db->getColumnData("SELECT $getItem FROM memberabout WHERE MemberID = ?", array($memberid));
        switch ($items->NavID) {
          case 1:
            $requirePage = "editbirthday.php";
            $contents_about = explode("-", $contents_about);
            $contents_about = $contents_about[2] . " " . getmonth($contents_about[1]) . " " . $contents_about[0];
            break;
          case 2:
            $requirePage =  "edituniversity.php";
            $contents_about = $db->getColumnData("SELECT UniversityName FROM universities WHERE UniversityID = ?", array($contents_about));
            break;
          case 3:
            $requirePage = "editfaculty.php";
            $contents_about = $db->getColumnData("SELECT FacultyName FROM faculties_$language WHERE FacultyID = ?", array($contents_about));
            break;
          case 4:
            $requirePage =  "editdepartment.php";
            $contents_about = $db->getColumnData("SELECT DepartmentName FROM departments_$language WHERE DepartmentID = ?", array($contents_about));
            break;
          case 5:
            $requirePage =  "edithobbies.php";
            break;
          case 6:
            $requirePage =  "edittv.php";
            break;
          case 7:
            $requirePage =  "editcountry.php";
            $contents_about = $db->getColumnData("SELECT CountryName FROM countries WHERE CountryID = ?", array($contents_about));
            break;
          case 8:
          case 9:
            $requirePage =  "editcity.php";
            $contents_about = $db->getColumnData("SELECT CityName FROM cities WHERE CityID = ?", array($contents_about));
            break;
          default:
            break;
        }
        $contents_about = ($contents_about == '' ? $translates["undefined"] : $contents_about);
      ?>
        <tr class="text-center border-bottom" id="<?= $items->NavForm ?>">
          <th class="py-3 col-3 border-end"><?= $items->NavName ?></th>
          <td class="py-3 col-6" id="contents_about_<?= $items->NavForm ?>"><?= $contents_about ?></td>
          <td class="edit-info py-3 col-3 border-start" onClick="EditOpen('<?= $items->NavForm ?>')"><?= $translates["edit"] ?> <i class="fas fa-pen" style="font-size:14px"></i></td>
        </tr>
        <tr class="text-center border-bottom d-none" id="edit_<?= $items->NavForm ?>">
          <?php require $requirePage; ?>
        </tr>
      <?php } ?>
    </table>
    <div id="about_result"></div>
  </div>
</div>