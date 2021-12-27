<?php

$allnews = $db->getDatas("SELECT * FROM news");

$NewsID = "";

foreach ($allnews as $news) {
    $Header = $news->NewsHeader;
    $seodHeader = seolink($Header) . "-" . $news->NewsID;
    if ($seodHeader == $part) {
        $NewsID = $news->NewsID;
    }
}

$myNews = $db->getData("SELECT * FROM news WHERE NewsID = ?", array($NewsID));

$NewsTime = $myNews->NewsAddTime;
$pattern = "/-/";
$NewsTime = preg_replace($pattern, ".", $NewsTime);

?>


<div class="container p-4">
    <div class="row m-4">
        <div class="col-12 text-center text-dark">
            <h2 style="font-size: 50px;"><?= $myNews->NewsHeader ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-4 text-start text-dark fs-5">
            <small><?= $translates["author"] . ": " . $myNews->NewsAuthor ?></small>
        </div>
        <div class="col-4 text-center text-dark fs-5">
            <small style="word-break:break-all;"><?= $translates["source"] . ": " . $myNews->NewsResource ?></small>
        </div>
        <div class="col-4 text-end text-dark fs-5">
            <small><?= myDate($NewsTime) . " " . messageTime($NewsTime) ?></small>
        </div>
    </div>
    <hr class="text-dark mb-5">
    <div class="row">
        <div class="col-12">
            <div class="d-md-none">
                <div class="row">
                    <div class="col-12">
                        <img src="news_images/<?= $myNews->NewsImg ?>" class="rounded-3 w-100 shadow">
                    </div>
                    <div class="col-12 mt-4">
                        <p class="fs-4 text-dark" style="text-indent:15px;"><?= $myNews->NewsContent ?></p>
                    </div>
                </div>
            </div>
            <div class="d-none d-md-block clearfix">
                <img src="news_images/<?= $myNews->NewsImg ?>" class="rounded-3 me-3 shadow" style="width: 600px;float:left !important;">
                <p class="fs-4 text-dark" style="text-indent:15px;-webkit-hyphens: auto !important;-moz-hyphens: auto !important;hyphens: auto !important;"><?= $myNews->NewsContent ?></p>
            </div>
        </div>
    </div>
</div>