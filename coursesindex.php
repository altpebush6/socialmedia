<div class="container border shadow mt-5">
    <?php if (!$part) { ?>
        <div class="row" style="height: 70vh;overflow:auto;">
            <div class="col-3 border-end shadow">
                <div class="row pt-2">
                    <div class="col-12 text-center fs-4 my-2"><?= $translates["filter"] ?></div>
                    <div class="col-12 mt-3">
                        <input type="text" class="form-control w-100 courseFilter" name="courseName" id="courseName" placeholder="<?= $translates["entercoursename"] ?>">
                    </div>
                    <div class="col-12 mt-3">
                        <input type="text" class="form-control w-100 courseFilter" name="courseCode" id="courseCode" placeholder="<?= $translates["entercoursecode"] ?>">
                    </div>
                    <div class="col-12 mt-3">
                        <select name="CourseClass" id="CourseClass" class="form-select courseFilter">
                            <option value="0" selected><?= $translates["selectclass"] ?></option>
                            <option value="1">1<?= $translates["class"] ?></option>
                            <option value="2">2<?= $translates["class"] ?></option>
                            <option value="3">3<?= $translates["class"] ?></option>
                            <option value="4">4<?= $translates["class"] ?></option>
                            <option value="5">5<?= $translates["class"] ?></option>
                            <option value="6">6<?= $translates["class"] ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="row justify-content-center align-items-center h-100">
                    <div class="col-12 m-0 p-0 px-3 text-center" id="courseContainer">
                        <?php $allcourses = $db->getDatas("SELECT * FROM courses");
                        foreach ($allcourses as $course) { ?>
                            <a class="btn btn-post shadow mx-1 mt-3" href="http://localhost/aybu/socialmedia/<?= $translates["courses"] . "/" . $course->CourseID ?>"><?= $course->CourseCode ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } else {
        $course = $db->getData("SELECT * FROM courses WHERE CourseID = ?", array($part));
    ?>
        <div class="row">
            <h4 class="text-center shadow p-3 mb-0"><?= $course->CourseName ?></h4>
            <div class="row" style="height: 62vh;overflow:auto;">
                <div class="col-3 shadow p-0 m-0">
                    <ul class="nav nav-tabs flex-column">
                        <?php $navs = $db->getDatas("SELECT * FROM nav_course_$language");
                        foreach ($navs as $nav) {
                            $isActive = $edit == $nav->NavLink ? "active" : "";
                        ?>
                            <li class="nav-item">
                                <a class="nav-link courseTabs <?= $isActive ?> p-3" aria-current="page" href="http://localhost/aybu/socialmedia/<?= $translates["courses"] . "/" . $course->CourseID . "/" . $nav->NavLink ?>"><?= $nav->NavName ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-9">
                    <?php if (!$edit) { ?>
                        <div class="row justify-content-center align-items-center h-100" id="coursePage">
                            <div class="col-6">
                                <ul class="list-group text-center shadow">
                                    <li class="list-group-item bg-transparent"><?= $translates["coursecode"] . ": " . $course->CourseCode ?></li>
                                    <li class="list-group-item bg-transparent"><?= $translates["courseakts"] . ": " . $course->CourseAkts ?></li>
                                    <li class="list-group-item bg-transparent" id="courseAttandance"><?= $translates["peoplecourse"] . ": " . $db->getColumnData("SELECT COUNT(*) FROM membercourses WHERE CourseID = ?",array($course->CourseID)) ?></li>
                                    <li class="list-group-item bg-transparent"><?= $course->CourseDscr ?></li>
                                </ul>
                                <?php
                                $isCourseHave = $db->getData("SELECT * FROM membercourses WHERE MemberID = ? AND CourseID = ?", array($memberid, $course->CourseID));
                                if (!$isCourseHave) { ?>
                                    <button type="button" class="btn btn-post w-100 mt-2 shadow courseattendance" courseid="<?= $course->CourseID ?>" id="enrollCourse"><?= $translates["addcourse"] ?></button>
                                <?php } else { ?>
                                    <button type="button" class="btn btn-secondary w-100 mt-2 shadow courseattendance" courseid="<?= $course->CourseID ?>" id="quitCourse"><?= $translates["hascourse"] ?> <i class="fas fa-check"></i></button>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else {
                        switch ($edit) {
                            case 'homeworks':
                            case 'odevler':
                                require_once "coursehw.php";
                                break;
                            case 'exercises':
                            case 'alistirmalar':
                                require_once "courseexercises.php";
                                break;
                            case 'questions':
                            case 'sorular':
                                require_once "coursequestions.php";
                                break;
                            case 'notes':
                            case 'notlar':
                                require_once "coursenotes.php";
                                break;
                            case 'videos':
                            case 'videolar':
                                require_once "coursevideos.php";
                                break;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>