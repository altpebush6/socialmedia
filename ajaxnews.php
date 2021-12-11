<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";
require_once "functions/time.php";

$db = new aybu\db\mysqlDB();
$SS = new aybu\session\session();

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}
require_once "languages/language_" . $language . ".php";

$AdminID = $SS->get("AdminID");

$operation = $_GET['operation'];
$result = array();

switch ($operation) {
    case 'openNews':
        $NewsID = security("NewsID");
        $db->Update("UPDATE news SET NewsActiveness = ? WHERE NewsID = ?", array(1, $NewsID));
        $result["state"] = "News's Been Opened";
        $result["NewsID"] = $NewsID;
        echo json_encode($result);
        break;

    case 'closeNews':
        $NewsID = security("NewsID");
        $db->Update("UPDATE news SET NewsActiveness = ? WHERE NewsID = ?", array(0, $NewsID));
        $result["state"] = "News's Been Closed";
        $result["NewsID"] = $NewsID;
        echo json_encode($result);
        break;

    case 'createNews':
        $NewsImg = $_FILES['NewsFile']['name'];
        if ($NewsImg) {
            $NewsImg_ext = strtolower(pathinfo($NewsImg, PATHINFO_EXTENSION));
            $allowed_file_extensions = array("png", "jpg", "jpeg", "jfif");
            if (!in_array($NewsImg_ext, $allowed_file_extensions)) {
                $result["error"] = "Sadece jpeg, jpg, png ve jfif uzantılı dosya yükleyebilirsiniz.";
            } else {
                $NewsImg = $NewsHeader . "_" . uniqid() . "." . $NewsImg_ext;
                $target = "news_images/" . basename($NewsImg);
            }
            move_uploaded_file($_FILES['NewsFile']['tmp_name'], $target);
        } else {
            $NewsImg = "noneimage.png";
        }

        $NewsHeader = security("Newsheader");
        $Newsauthor = security("Newsauthor");
        $NewsResource = security("NewsResource");
        $NewsContentSummarize = security("NewsContentSummarize");
        $NewsContent = $_POST["NewsContent"];
        $lastOrder = $db->getColumnData("SELECT NewsOrder FROM news ORDER BY NewsOrder DESC");
        if ($lastOrder) {
            $newsOrder = $lastOrder + 1;
        } else {
            $newsOrder = 1;
        }


        if (empty($NewsHeader) || empty($Newsauthor) || empty($NewsResource) || empty($NewsContentSummarize) || empty($NewsContent)) {
            $result["error"] = "Boş Alan Bırakılamaz!";
        } else {
            $AddNews = $db->Insert("INSERT INTO news
                                    SET NewsImg = ?,
                                    NewsHeader = ?,
                                    NewsSummarize = ?,
                                    NewsContent = ?,
                                    NewsAuthor = ?,
                                    NewsResource = ?,
                                    NewsOrder = ?,
                                    AdminID = ?", array($NewsImg, $NewsHeader, $NewsContentSummarize, $NewsContent, $Newsauthor, $NewsResource, $newsOrder, $AdminID));

            if ($AddNews) {
                $orderCount = $newsOrder;
                $result["news"] .=  '<div class="card pt-2 my-2 m-2" style="width: 18rem;">
                                        <img src="news_images/' . $NewsImg . '" class="card-img-top" style="height:23vh;">
                                        <div class="card-body" style="height:25vh;">
                                            <h5 class="card-title">' . $NewsHeader . '</h5>
                                            <p class="card-text">' . $NewsContentSummarize . '</p>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Yazar: ' . $Newsauthor . '</li>
                                            <li class="list-group-item">Kaynak: ' . $NewsResource . '</li>
                                            <li class="list-group-item">Sıra:
                                            <select class="py-1 px-2 news_order" name="news_order" id="news_order_' . $AddNews . '" newsid="' . $AddNews . '">';
                for ($i = 1; $i <= $orderCount; $i++) {
                    if ($i == $newsOrder) {
                        $result["news"] .= '<option class="py-1 px-2" id="option_' . $AddNews . '_' . $i . '" value="' . $i . '" disabled selected>' . $i . '</option>';
                    } else {
                        $result["news"] .= '<option class="py-1 px-2" id="option_' . $AddNews . '_' . $i . '" value="' . $i . '">' . $i . '</option>';
                    }
                }
                $result["news"] .= '</select>
                                        </li>
                                        </ul>
                                        <div class="card-body d-flex justify-content-between align-items-center">
                                            <button type="button" newsid="' . $AddNews . '" class="editnews btn btn-dark btn-sm">Haberi Düzenle <span class="spinner mx-auto" id="editnewsspinner_' . $AddNews . '"></span></button>
                                            <div class="form-check form-switch ms-4 text-center">
                                                <input class="form-check-input newsActiveness" style="font-size:25px;" newsID="' . $AddNews . '" type="checkbox" checked>
                                            </div>
                                            <i class="fas fa-trash text-danger" onClick=\'deleteNews("' . $AddNews . '")\' style="font-size:25px;cursor:pointer;"></i>
                                        </div>
                                    </div>';
            }
        }
        if ($AdminID == 1) {
            $result["countNews"] = ($db->getColumnData("SELECT COUNT(*) FROM news LIMIT 4")) - 1;
        } else {
            $result["countNews"] = ($db->getColumnData("SELECT COUNT(*) FROM news WHERE AdminID = ? LIMIT 4", array($AdminID))) - 1;
        }
        $result["newsID"] = $AddNews;
        $result["newsOrder"] = $newsOrder;
        echo json_encode($result);
        break;

    case 'showNews':
        $lastid = security("LastID");
        $result["lastid"] = $lastid;
        if ($AdminID == 1) {
            $newNews = $db->getDatas("SELECT * FROM news WHERE NewsID > $lastid LIMIT 4");
        } else {
            $newNews = $db->getDatas("SELECT * FROM news WHERE NewsID > $lastid AND AdminID = ? LIMIT 4", array($AdminID));
        }
        $orderCount = $db->getColumnData("SELECT COUNT(*) FROM news WHERE NewsActiveness = ?", array(1));
        foreach ($newNews as $news) {
            $activeness = $news->NewsActiveness;
            if ($activeness == 1) {
                $ischecked = "checked";
            } else {
                $ischecked = "";
            }
            $result["news"] .=  '<div class="card pt-2 my-2 m-2" style="width: 18rem;">
            <img src="news_images/' . $news->NewsImg . '" class="card-img-top" style="height:23vh;">
            <div class="card-body" style="height:25vh;">
                <h5 class="card-title">' . $news->NewsHeader . '</h5>
                <p class="card-text">' . $news->NewsSummarize . '</p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Yazar: ' . $news->NewsAuthor . '</li>
                <li class="list-group-item">Kaynak: ' . $news->NewsResource . '</li>
                <li class="list-group-item">Sıra:
                <select class="py-1 px-2 news_order" name="news_order" id="news_order_' . $news->NewsID . '" newsid="' . $news->NewsID . '">';

            for ($i = 1; $i <= $orderCount; $i++) {
                if ($i == $news->NewsOrder) {
                    $result["news"] .= '<option class="py-1 px-2" id="option_' . $news->NewsID . '_' . $i . '" value="' . $i . '" disabled selected>' . $i . '</option>';
                } else {
                    $result["news"] .= '<option class="py-1 px-2" id="option_' . $news->NewsID . '_' . $i . '" value="' . $i . '">' . $i . '</option>';
                }
            }
            $result["news"] .= '</select>
            </li>
            </ul>
            <div class="card-body d-flex justify-content-between align-items-center">
                <button type="button" newsid="' . $news->NewsID . '" class="editnews btn btn-dark btn-sm">Haberi Düzenle <span class="spinner mx-auto" id="editnewsspinner_' . $news->NewsID . '"></span></button>
                <div class="form-check form-switch ms-4 text-center">
                    <input class="form-check-input newsActiveness" style="font-size:25px;" newsID="' . $news->NewsID . '" type="checkbox" ' . $ischecked . '>
                </div>
                <i class="fas fa-trash text-danger" onClick=\'deleteNews("' . $news->NewsID . '")\' style="font-size:25px;cursor:pointer;"></i>
            </div>
        </div>';
        }
        $newsremain = $db->getColumnData("SELECT COUNT(*) FROM news WHERE NewsID > $lastid+4");
        $result["newsremain"] = $newsremain;
        $result["lastid"] = $news->NewsID;
        echo json_encode($result);
        break;

    case 'editnews':
        $newsid = security("NewsID");
        $newsinfos = $db->getData("SELECT * FROM news WHERE NewsID = ?", array($newsid));
        $NewsTime = $newsinfos->NewsAddTime;
        $pattern = "/-/";
        $NewsTime = preg_replace($pattern, ".", $NewsTime);
        $result["output"] = '<div class="container p-4">
                                <div class="row m-4">
                                    <div class="col-12 text-center text-dark">
                                        <h2 style="font-size: 50px;" id="editnewsHeader">' . $newsinfos->NewsHeader . '</h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 text-start text-dark fs-5">
                                        <small id="editnewsAuthor"> Yazar: ' . $newsinfos->NewsAuthor . '</small>
                                    </div>
                                    <div class="col-4 text-center text-dark fs-5">
                                        <small id="editnewsResource"> Kaynak: '  . $newsinfos->NewsResource . '</small>
                                    </div>
                                    <div class="col-4 text-end text-dark fs-5">
                                        <small>' . myDate($NewsTime) . " " . messageTime($NewsTime) . '</small>
                                    </div>
                                </div>
                                <hr class="text-dark mb-5">
                                <div class="row">
                                    <div class="col-12">
                                        <img id="editnewsImg" src="news_images/' . $newsinfos->NewsImg . '" class="rounded-3 me-3" style="width: 600px;float:left !important;">
                                        <p id="editnewsContent" class="fs-4 text-dark" style="text-indent:15px;">' . $newsinfos->NewsContent . '</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row justify-content-center fs-3 py-3">Haberi Düzenle</div>
                                <div class="row">
                                    <form method="post" id="EditnewsForm" newsid="' . $newsinfos->NewsID . '">
                                        <div class="col-12">
                                            <div class="row my-3">
                                                <label for="EditNewsheading" class="col-2 fs-5 form-label text-end">Başlık:</label>
                                                <div class="col-10">
                                                    <input class="form-control" type="text" value="' . $newsinfos->NewsHeader . '" name="EditNewsheader" id="EditNewsheader" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="row my-3">
                                                <label for="EditNewsFile" class="col-2 fs-5 form-label text-end">Haberin Fotoğrafı:</label>
                                                <div class="col-10">
                                                    <input class="form-control" id="EditNewsFile" name="EditNewsFile" type="file">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="EditNewsauthor" class="col-2 fs-5 form-label text-end">Yazar:</label>
                                            <div class="col-10">
                                                <input class="form-control" type="text" value="' . $newsinfos->NewsAuthor . '" name="EditNewsauthor" id="EditNewsauthor" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="NewsResource" class="col-2 fs-5 form-label text-end">Kaynak:</label>
                                            <div class="col-10">
                                                <input class="form-control" type="text" value="' . $newsinfos->NewsResource . '" name="EditNewsResource" id="EditNewsResource" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="NewsResource" class="col-2 fs-5 form-label text-end">İçerik Özeti:</label>
                                            <div class="col-10">
                                                <input class="form-control" type="text" value="' . $newsinfos->NewsSummarize . '" name="EditNewsContentSummarize" id="EditNewsContentSummarize" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="NewsResource" class="col-2 fs-5 form-label text-end">İçerik:</label>
                                            <div class="col-10">
                                                <input class="form-control" name="EditNewsContent" id="EditNewsContent" value="' . $newsinfos->NewsContent . '"></input>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-10 offset-2">
                                                <button type="submit" id="EditsubmitNews" class="btn btn-dark w-100">Haberi Düzenle <span class="spinner" id="editnews_spinner"></span></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>';
        echo json_encode($result);
        break;

    case 'submitedittednews':
        $newsID = $_GET["NewsID"];
        $NewsHeader = security("EditNewsheader");
        $NewsImg = $_FILES['EditNewsFile']['name'];
        if ($NewsImg) {
            $result["imagename"] = "var";
            $NewsImg_ext = strtolower(pathinfo($NewsImg, PATHINFO_EXTENSION));
            $allowed_file_extensions = array("png", "jpg", "jpeg", "jfif");
            if (!in_array($NewsImg_ext, $allowed_file_extensions)) {
                $result["error"] = "Sadece jpeg, jpg, png ve jfif uzantılı dosya yükleyebilirsiniz.";
            } else {
                $NewsImg = $NewsHeader . "_" . uniqid() . "." . $NewsImg_ext;
                $target = "news_images/" . basename($NewsImg);
            }
            move_uploaded_file($_FILES['EditNewsFile']['tmp_name'], $target);
        } else {
            $oldpicture = $db->getColumnData("SELECT NewsImg FROM news WHERE NewsID = ?", array($newsID));
            $NewsImg = $oldpicture;
        }

        $Newsauthor = security("EditNewsauthor");
        $NewsResource = security("EditNewsResource");
        $NewsContentSummarize = security("EditNewsContentSummarize");
        $NewsContent = security("EditNewsContent");

        if (empty($NewsHeader) || empty($Newsauthor) || empty($NewsResource) || empty($NewsContentSummarize) || empty($NewsContent)) {
            $result["error"] = "Boş Alan Bırakılamaz!";
        } else {
            $AddNews = $db->Update("UPDATE news
                                    SET NewsImg = ?,
                                    NewsHeader = ?,
                                    NewsSummarize = ?,
                                    NewsContent = ?,
                                    NewsAuthor = ?,
                                    NewsResource = ? WHERE NewsID = ?", array($NewsImg, $NewsHeader, $NewsContentSummarize, $NewsContent, $Newsauthor, $NewsResource, $newsID));

            if ($AddNews) {
                $result["EdittedNewsHeader"] = $NewsHeader;
                $result["EdittedNewsImg"] = $NewsImg;
                $result["EdittedNewsContentSummarize"] = $NewsContentSummarize;
                $result["EdittedNewsContent"] = $NewsContent;
                $result["EdittedNewsauthor"] = $Newsauthor;
                $result["EdittedNewsResource"] = $NewsResource;
                $result["NewsID"] = $newsID;
            }
        }
        echo json_encode($result);
        break;

    case 'deletenews':
        $newsID = $_GET["NewsID"];
        $newsOrder = $db->getColumnData("SELECT NewsOrder FROM news WHERE NewsID = ?", array($newsID));
        $deletenews = $db->Delete("DELETE FROM news WHERE NewsID = ?", array($newsID));
        //Bu haberden sıra olarak sonrakilerin sırasını 1 düşür
        $allnews = $db->getDatas("SELECT * FROM news WHERE NewsOrder>$newsOrder");
        foreach ($allnews as $news) {
            $newsID = $news->NewsID;
            $newOrder = $news->NewsOrder - 1;
            $db->Update("UPDATE news SET NewsOrder = ? WHERE NewsID = ?", array($newOrder,$newsID));
        }
        $result["success"] = $newsID;
        echo json_encode($result);
        break;

    case 'changeorder':
        $newsID = security("NewsID");
        $newOrder = security("NewOrder");
        $oldOrder = $db->getColumnData("SELECT NewsOrder FROM news WHERE NewsID = ?", array($newsID));
        $NewordersNewsID = $db->getColumnData("SELECT NewsID FROM news WHERE NewsOrder = ?", array($newOrder));
        $changeOrder1 = $db->Update("UPDATE news SET NewsOrder = ? WHERE NewsID = ?", array($newOrder, $newsID));
        $changeOrder2 = $db->Update("UPDATE news SET NewsOrder = ? WHERE NewsID = ?", array($oldOrder, $NewordersNewsID));
        if ($changeOrder1 and $changeOrder2) {
            $result["success"] = "ok";
            $result["otherNewsID"] = $NewordersNewsID;
            $result["oldOrder"] = $oldOrder;
        } else {
            $result["error"] = "ok";
        }
        echo json_encode($result);
        break;
}
