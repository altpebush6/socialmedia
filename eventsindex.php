<?php if ($part) {
    require_once "eventpage.php";
?>

<?php } else {
    $pageNum = $_GET["pageNum"];
    if (empty($pageNum)) {
        $pageNum = 1;
    }
    $category = $_GET["category"];
    if (!empty($category)) {
        $category = "&category=" . $category;
    }
    $uni = $_GET["uni"];
    if (!empty($uni)) {
        $uni = "&uni=" . $uni;
    }
    $price = $_GET["price"];
    if (!empty($price)) {
        $price = "&price=" . $price;
    }
    $order = $_GET["order"];
    if (!empty($order)) {
        $order = "&order=" . $order;
    }

?>
    <div class="categorize">
        <div class="row w-100 m-0 p-0 text-dark">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 mt-2">
                        <h5 class="text-center"><?= $translates["categories"] ?></h5>
                    </div>
                    <hr>
                    <form name="form_categorize" id="form_categorize" method="post">
                        <div class="col-12">
                            <h6></h6>
                            <input type="text" id="search_school" class="form-control form-control-sm w-100 bg-transparent text-dark" placeholder="<?= $translates["schoolname"] ?>">
                        </div>
                        <div class="col-12 mt-1">
                            <ul class="list-group border m-0" style="max-height: 20vh;overflow-y:auto;overflow-x:hidden;min-height: 144px;" id="all_schools">
                                <?php

                                $universities = $db->getDatas("SELECT * FROM universities ORDER BY UniversityName ASC");
                                foreach ($universities as $university) {
                                    $ischecked = "";
                                    $pattern1 = "/-/";
                                    if (preg_match($pattern1, $_GET["uni"])) {
                                        $unis = explode("-", $_GET["uni"]);
                                        if (in_array($university->UniversityID, $unis)) {
                                            $ischecked = "checked";
                                        }
                                    } else {
                                        if ($_GET["uni"] == $university->UniversityID) {
                                            $ischecked = "checked";
                                        }
                                    }
                                ?>
                                    <li class="list-group-item bg-transparent text-dark m-0 p-2">
                                        <div class="row pe-3">
                                            <div class="col-2">
                                                <input type="checkbox" class="form-check-input me-4 selectuni" id="<?= $university->UniversityID ?>" <?= $ischecked ?> style="cursor:pointer;">
                                            </div>
                                            <div class="col-10" style=" white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                                                <label for="<?= $university->UniversityID ?>" title="<?= $university->UniversityName ?>" style="cursor:pointer;"><?= $university->UniversityName ?></label>
                                            </div>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="col-12 mt-3">
                            <h6><?= $translates["price"] ?></h6>
                        </div>
                        <div class="col-12 mb-3 d-flex flew-row">
                            <div class="row">
                                <div class="col-8 d-flex flex-row m-0 p-0 ps-2">
                                    <input type="text" name="least_price" id="least_price" class="form-control form-control-sm me-1" placeholder="<?= $translates["least"] ?>">-
                                    <input type="text" name="most_price" id="most_price" class="form-control form-control-sm ms-1" placeholder="<?= $translates["most"] ?>">
                                </div>
                                <div class="col-2 m-0 p-0 ps-2 text-start">
                                    <button type="button" id="categorize_items" class="btn btn-sm btn-primary"><?= $translates["search"] ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="order">
        <div class="row w-100 m-0 p-0 text-dark">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 mt-2">
                        <h5 class="text-center"><?= $translates["order"] ?></h5>
                    </div>
                    <hr>
                    <div class="col-12 mt-1">
                        <h6><?= $translates["selectorder"] ?></h6>
                    </div>
                    <div class="col-12 mb-4">
                        <ul class="list-group">
                            <a href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $pageNum . $category . $uni . $price ?>" class="list-group-item order-events"><?= $translates["default"] ?></a>
                            <a href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $pageNum . $category . $uni . $price ?>&order=EventPrice_ASC" class="list-group-item order-events"><?= $translates["leastprice"] ?></a>
                            <a href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $pageNum . $category . $uni . $price ?>&order=EventPrice_DESC" class="list-group-item order-events"><?= $translates["mostprice"] ?></a>
                            <a href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $pageNum . $category . $uni . $price ?>&order=EventParticipant_DESC" class="list-group-item order-events"><?= $translates["mostinteractions"] ?></a>
                            <a href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $pageNum . $category . $uni . $price ?>&order=EventAddTime_DESC" class="list-group-item order-events"><?= $translates["newests"] ?></a>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-3">
        <!-- BUTONLAR -->
        <div class="row">
            <div class="col-4 text-start">
                <button type="button" id="toggle_categorize" class="btn btn-outline-theme"><?= $translates["togglecat"] ?></button>
            </div>
            <div class="col-4 text-center">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createEvent"><?= $translates["createevent"] ?></button>
            </div>
            <div class="col-4 text-end">
                <button type="button" id="toggle_order" class="btn btn-outline-theme"><?= $translates["toggleorder"] ?></button>
            </div>
        </div>
        <!-- ETKİNLİK KATEGORİLERİ -->
        <div class="row mt-2 justify-content-center">
            <?php
            $categories = $db->getDatas("SELECT * FROM eventcategories_$language");
            ?>
            <div class="col-8 col-sm-9 col-md-10 pt-4">
                <div class="owl-carousel owl-theme d-flex justify-content-center" id="all_categories">
                    <?php
                    foreach ($categories as $eachcategory) {
                        $categoryIcon = $eachcategory->CategoryIcon;
                        $categoryName = $eachcategory->CategoryName;

                    ?>
                        <a href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $pageNum ?>&category=<?= $eachcategory->CategoryID . $uni . $price . $order ?>" class="item each_category border p-4 rounded-1 d-flex flex-column jusitfy-content-center align-items-center shadow my-4 text-decoration-none">
                            <?= $categoryIcon ?>
                            <span class="fs-4 text-dark mt-2 category_names"><?= $categoryName ?></span>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <hr class="my-4 text-dark">
        <!-- ETKİNLİKLER -->
        <div class="row mt-4 ps-events">
            <?php
            if ($_GET["order"]) {
                $remove_ = "/_/";
                $orderby = preg_replace($remove_, " ", $_GET["order"]);
            } else {
                $orderby = "EventID ASC";
            }

            $whereclause = "";

            if ($price) {
                $price = explode("-", $_GET["price"]);
                $least_price = $price["0"];
                $most_price = $price["1"];
                if ($most_price == "?") {
                    $whereclause .= " AND EventPrice>=" . $least_price;
                } else {
                    $whereclause .= " AND " . $most_price . ">=EventPrice AND EventPrice>=" . $least_price;
                }
            }

            if ($_GET["category"]) {
                $whereclause .= " AND EventCategory=" . $_GET["category"];
            }

            if ($_GET["uni"]) {
                $pattern1 = "/-/";
                if (preg_match($pattern1, $_GET["uni"])) {
                    $unis = explode("-", $_GET["uni"]);
                    $whereclause .= " AND (EventSchool=" . $unis[0];
                    foreach ($unis as $uniID) {
                        if ($uniID != $unis[0]) {
                            $whereclause .= " OR EventSchool=" . $uniID;
                        }
                    }
                    $whereclause .= ")";
                } else {
                    $whereclause .= " AND EventSchool=" . $_GET["uni"];
                }
            }

            $limit = 9;
            $startlimit = ($pageNum * $limit) - $limit;
            $total_events = $db->getColumnData("SELECT COUNT(*) FROM events WHERE EventStatus = ? $whereclause", array(1));
            $maxPageNumber = ceil($total_events / $limit);
            $all_events = $db->getDatas("SELECT * FROM events WHERE EventStatus = ? $whereclause ORDER BY $orderby LIMIT ?,?", array(1, $startlimit, $limit));

            foreach ($all_events as $event) {
            ?>
                <div class="col-4 my-3 pe-5">
                    <div class="row rounded-3 w-100 each-event shadow <?= $event->EventPremium ? 'eventPre' : 'border' ?>" style="height:26vh;overflow:hidden;">
                        <div class="ribbon"><span>GOLD</span></div>
                        <div class="col-5 m-0 p-0" style="overflow: hidden;">
                            <img src="events_images/<?= $event->EventImage ?>" class="rounded-3 w-100" style="height:100%">
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
                            <div class="p-0 fs-3 text-end text-dark" style="height:2vh;">
                                <?= ($event->EventPrice == 0 ? $translates['free'] : $event->EventPrice . "₺") ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- SAYFALAMA -->
    <?php if ($total_events > 0) {
        $others =  $category . $uni . $price . $order;
    ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php
                if ($pageNum > 1) {
                    $newPage = $pageNum - 1;
                ?>
                    <li class="page-item">
                        <a class="page-link" href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $newPage . $others ?>"><?= $translates["previous"] ?></a>
                    </li>
                <?php } else { ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $newPage . $others ?>"><?= $translates["next"] ?></a>
                    </li>
                    <?php }

                $interval = 2;
                for ($i = $pageNum - $interval; $i <= $pageNum + $interval; $i++) {
                    if ($i > 0 and $i <= $maxPageNumber) {
                        if ($i == $pageNum) {
                    ?>
                            <li class="page-item active"><a class="page-link" href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $i . $others ?>"><?= $i ?></a></li>
                        <?php } else { ?>
                            <li class="page-item"><a class="page-link" href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $i . $others ?>"><?= $i ?></a></li>
                    <?php }
                    }
                }

                if ($maxPageNumber != $pageNum) {
                    $newPage = $pageNum + 1;
                    ?>
                    <li class="page-item">
                        <a class="page-link" href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $newPage . $others ?>">Next</a>
                    </li>
                <?php } else { ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="http://localhost/aybu/socialmedia/<?= $translates["events"] ?>?pageNum=<?= $newPage . $others ?>">Next</a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    <?php } ?>
    <!-- ETKİNLİK OLUŞTUR -->
    <div class="modal fade" id="createEvent">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEventLabel"><?= $translates["createevent"] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height:75vh;overflow-y:auto">
                    <form method="post" id="form_createEvent" autocomplete="off">
                        <div id="first_sec">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="eventHeader" class="form-label text-muted"><?= $translates["eventheader"] ?>*</label>
                                    <input class="form-control" type="text" name="eventHeader" id="eventHeader" maxlength="100" placeholder="<?= $translates["entereventheader"] ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="eventImg" class="form-label text-muted"><?= $translates["eventimage"] ?>*</label>
                                    <input class="form-control" id="eventImg" name="eventImg" type="file">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="eventCategory" class="form-label text-muted"><?= $translates["eventcategory"] ?>*</label>
                                    <select class="form-select" name="eventCategory" id="eventCategory">
                                        <option value="0" selected disabled><?= $translates["selectCategory"] ?></option>
                                        <?php
                                        $categories = $db->getDatas("SELECT * FROM eventcategories_$language");
                                        foreach ($categories as $category) { ?>
                                            <option value="<?= $category->CategoryID ?>"><?= $category->CategoryName ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="second_sec" class="d-none">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="eventSchool" class="form-label text-muted"><?= $translates["eventschool"] ?></label>
                                    <select class="form-select" name="eventSchool" id="eventSchool">
                                        <option value="0" selected disabled><?= $translates["selectschool"] ?></option>
                                        <?php
                                        $universities = $db->getDatas("SELECT * FROM universities ORDER BY UniversityName ASC");
                                        foreach ($universities as $university) { ?>
                                            <option value="<?= $university->UniversityID ?>"><?= $university->UniversityName ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-1" id="cityofEvent">
                                <div class="col-12">
                                    <label for="eventCity" class="form-label text-muted"><?= $translates["eventcity"] ?>*</label>
                                    <select class="form-select" name="eventCity" id="eventCity">
                                        <option value="0" selected disabled><?= $translates["selectcity"] ?></option>
                                        <?php
                                        $cities = $db->getDatas("SELECT * FROM cities");
                                        foreach ($cities as $city) { ?>
                                            <option value="<?= $city->CityID ?>"><?= $city->CityName ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <input type="checkbox" class="form-check-input" id="noCity" name="noCity" style="cursor: pointer;">
                                    <label for="noCity" class="form-check-label" style="cursor: pointer;"><?= $translates["nocity"] ?></label>
                                </div>
                            </div>
                            <div class="row mt-1" id="placeofEvent" style="display: none;">
                                <div class="col-12">
                                    <input type="text" class="form-control" id="eventPlace" name="eventPlace" placeholder="<?= $translates["enterplace"] ?>">
                                </div>
                            </div>
                            <div class="row mt-3 mb-3">
                                <div class="col-12">
                                    <label for="eventDate" class="form-label text-muted"><?= $translates["eventdate"] ?>*</label>
                                    <input type="text" class="form-control" id="eventDate" name="eventDate" placeholder="<?= $translates["eventdate"] ?>">
                                </div>
                            </div>
                        </div>
                        <div id="third_sec" class="d-none">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="emailAddress" class="form-label text-muted"><?= $translates["contactmail"] ?>*</label>
                                    <input type="text" class="form-control" id="emailAddress" name="emailAddress" placeholder="<?= $translates["youremail"] ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="phoneNum" class="form-label text-muted"><?= $translates["contactphone"] ?>*</label>
                                    <input type="text" class="form-control" id="phoneNum" name="phoneNum" placeholder="<?= $translates["yourphone"] ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="explanation" class="form-label text-muted"><?= $translates["explanation"] ?></label>
                                    <textarea type="text" class="form-control" id="explanation" maxlength="1000" name="explanation" placeholder="<?= $translates["enterexplanation"] ?>" style="resize: none;"></textarea>
                                    <div class="form-text text-end"><span id="char_left">1000</span> <?= $translates["charleft"] ?></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="pricing" class="form-label text-muted"><?= $translates["price"] ?>*</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" maxlength="6" id="pricing" name="pricing" placeholder="<?= $translates["enterprice"] ?>">
                                        <div class="input-group-text">₺</div>
                                    </div>
                                    <div class="form-text"><?= $translates["pricenote"] ?></div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer m-0 p-0" id="footer_result" style="border:none !important;">
                    <p id="result"></p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="current_sec" currentsec="first_sec">
                    <button type="button" id="back_btn" class="btn btn-danger w-25 d-none"><?= $translates["back"] ?> <span class="spinner" id="spinnerback"></span></button>
                    <button type="button" id="continue_btn" class="btn btn-success w-100"><?= $translates["continue"] ?> <span class="spinner" id="spinnercontinue"></span></button>
                    <button type="submit" id="createEvent_btn" class="btn btn-primary w-70 d-none" operation="createEvent" eventid="0"><?= $translates["createtheevent"] ?> <span class="spinner" id="spinnercreateEvent"></span></button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $("#toggle_categorize").on("click", function() {
                $(".categorize").toggle("slide");
            });
            $("#toggle_order").on("click", function() {
                $(".order").toggle("slide");
            });
            $('.owl-carousel').owlCarousel({
                loop: false,
                margin: 10,
                rewind: false,
                center: false,
                autoplay: true,
                autoplayTimeout: 2000,
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
                $("#all_categories").trigger('prev.owl.carousel', [1000]);
            });
            $("#nextBtn").click(function() {
                $("#all_categories").trigger('next.owl.carousel', [1000]);
            });
        });
    </script>
<?php } ?>