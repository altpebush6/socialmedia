<div class="container border shadow mt-5">
    <?php if (!$part) { ?>
        <div class="row" style="min-height: 70vh;">
            <div class="col-3 border-end shadow">
                <div class="row pt-2">
                    <div class="col-12 text-center fs-5">Filtreler</div>
                    <div class="col-12 mt-2">
                        <input type="text" class="form-control w-100" placeholder="Enter department name">
                    </div>
                    <div class="col-12 mt-2">
                        <select name="" id="" class="form-select">
                            <option value="0" disabled selected>Choose your class</option>
                            <option value="1">1st Class</option>
                            <option value="2">2nd Class</option>
                            <option value="3">3rd Class</option>
                            <option value="4">4th Class</option>
                            <option value="5">5th Class</option>
                            <option value="6">6th Class</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="row position-absolute">
                    <div class="col-12 d-flex flex-row justify-content-around pt-2">
                        <select name="" id="" class="form-select mx-3">
                            <option value="">İsme göre filtreler</option>
                        </select>
                        <select name="" id="" class="form-select mx-3">
                            <option value="">İsme göre filtreler</option>
                        </select>
                        <select name="" id="" class="form-select mx-3">
                            <option value="">İsme göre filtreler</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex flex-row pt-2 px-4">
                        <input type="text" class="form-control mx-5" placeholder="Enter course name">
                    </div>
                </div>
                <div class="row justify-content-center align-items-center h-100">
                    <div class="col-12 m-0 p-0 px-3 text-center">
                        <?php $allcourses = $db->getDatas("SELECT * FROM courses");
                        foreach ($allcourses as $course) { ?>
                            <a class="btn btn-success shadow mx-1 mt-3" href="http://localhost/aybu/socialmedia/<?= $translates["courses"] . "/" . $course->CourseID ?>"><?= $course->CourseCode ?></a>
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
            <div class="row">
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
                        <div class="row justify-content-center align-items-center h-100">
                            <div class="col-6">
                                <ul class="list-group text-center shadow">
                                    <li class="list-group-item bg-transparent"><?= $translates["coursecode"] . ": " . $course->CourseCode ?></li>
                                    <li class="list-group-item bg-transparent"><?= $translates["courseakts"] . ": " . $course->CourseAkts ?></li>
                                    <li class="list-group-item bg-transparent"><?= $course->CourseDscr ?></li>
                                </ul>
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