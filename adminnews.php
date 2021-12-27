<div class="row" id="allContainer">
    <div class="col-12 border bg-light p-4" id="containerNews">
        <div class="row">
            <div class="col-12">

                <div class="row justify-content-center fs-3 py-3">All News</div>
                <div class="row" id="allnews">
                    <?php
                    if ($adminid == 1) {
                        $allnews = $db->getDatas("SELECT * FROM news ORDER BY NewsOrder ASC LIMIT 4");
                        $newsCounter = $db->getColumnData("SELECT COUNT(*) FROM news");
                    } else {
                        $allnews = $db->getDatas("SELECT * FROM news WHERE AdminID = ? ORDER BY NewsOrder ASC LIMIT 4", array($adminid));
                        $newsCounter = $db->getColumnData("SELECT COUNT(*) FROM news WHERE AdminID = ?", array($adminid));
                    }
                    foreach ($allnews as $news) {
                        $activeness = $news->NewsActiveness;
                        if ($activeness == 1) {
                            $ischecked = "checked";
                        } else {
                            $ischecked = "";
                        }
                        $orderCount = $db->getColumnData("SELECT COUNT(*) FROM news");
                    ?>
                        <div id="News_<?= $news->NewsID ?>" class="card pt-2 my-2 m-2" style="width: 18rem;">
                            <img src="news_images/<?= $news->NewsImg ?>" class="card-img-top" style="height:23vh;">
                            <div class="card-body" style="height:25vh;">
                                <h5 class="card-title"><?= $news->NewsHeader ?></h5>
                                <p class="card-text"><?= $news->NewsSummarize ?></p>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Yazar: <?= $news->NewsAuthor ?></li>
                                <li class="list-group-item">Kaynak: <?= $news->NewsResource ?></li>
                                <li class="list-group-item">Sıra:
                                    <select class="py-1 px-2 news_order" name="news_order" id="news_order_<?= $news->NewsID ?>" newsid="<?= $news->NewsID ?>">
                                        <?php
                                        for ($i = 1; $i <= $orderCount; $i++) {
                                            if ($i == $news->NewsOrder) { ?>
                                                <option class="py-1 px-2" value="<?= $i ?>" id="option_<?= $news->NewsID ?>_<?= $i ?>" disabled selected><?= $i ?></option>
                                            <?php } else { ?>
                                                <option class="py-1 px-2" id="option_<?= $news->NewsID ?>_<?= $i ?>" value="<?= $i ?>"><?= $i ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </li>
                            </ul>
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <button type="button" newsid="<?= $news->NewsID ?>" class="editnews btn btn-dark btn-sm">Haberi Düzenle <span class="spinner mx-auto" id="editnewsspinner_<?= $news->NewsID ?>"></span></button>
                                <div class="form-check form-switch ms-4 text-center">
                                    <input class="form-check-input newsActiveness" style="font-size:25px;" newsID="<?= $news->NewsID ?>" type="checkbox" <?= $ischecked ?>>
                                </div>
                                <i class="fas fa-trash text-danger" onClick="deleteNews('<?= $news->NewsID ?>')" style="font-size:25px;cursor:pointer;"></i>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php if ($newsCounter > 4) { ?>
                <div class="col-12 d-flex py-4">
                    <span class="spinner mx-auto" id="showmorenews"></span>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" id="showmorebtn" class="btn btn-primary w-100" lastid="<?= $news->NewsID ?>">Daha Fazla Göster</button>
                </div>
            <?php } ?>
            <hr class="my-5">
            <div class="col-12">
                <div class="row justify-content-center fs-3 py-3">Create News</div>
                <div class="row">
                    <form method="post" id="newsForm">
                        <div class="col-12">
                            <div class="row my-3">
                                <label for="Newsheading" class="col-2 fs-5 form-label text-end">Başlık:</label>
                                <div class="col-10">
                                    <input class="form-control" type="text" placeholder="Başlık Giriniz..." name="Newsheader" id="Newsheader" autocomplete="off">
                                </div>
                            </div>
                            <div class="row my-3">
                                <label for="NewsFile" class="col-2 fs-5 form-label text-end">Haberin Fotoğrafı:</label>
                                <div class="col-10">
                                    <input class="form-control" id="NewsFile" name="NewsFile" type="file">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="Newsauthor" class="col-2 fs-5 form-label text-end">Yazar:</label>
                            <div class="col-10">
                                <input class="form-control" type="text" placeholder="Adınızı Soyadınızı Giriniz..." name="Newsauthor" id="Newsauthor" autocomplete="off">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="NewsResource" class="col-2 fs-5 form-label text-end">Kaynak:</label>
                            <div class="col-10">
                                <input class="form-control" type="text" placeholder="Haberin Kaynağını Giriniz..." name="NewsResource" id="NewsResource" autocomplete="off">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="NewsResource" class="col-2 fs-5 form-label text-end">İçerik Özeti:</label>
                            <div class="col-10">
                                <input class="form-control" type="text" placeholder="İçeriğin Özetini Giriniz..." name="NewsContentSummarize" id="NewsContentSummarize" autocomplete="off">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="NewsResource" class="col-2 fs-5 form-label text-end">İçerik:</label>
                            <div class="col-10">
                                <textarea class="form-control" name="NewsContent" id="NewsContent" placeholder="İçeriği Giriniz..." cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-10 offset-2">
                                <button type="submit" id="submitNews" class="btn btn-dark w-100">Haberi Paylaş <span class="spinner" id="news_spinner"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    CKEDITOR.replace('NewsContent');
    CKEDITOR.replace('EditNewsContent');
</script>