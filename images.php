<div class="row">
    <div class="col-12 border bg-light p-4">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="my-4">All Members' Images</h2>
            </div>
            <div class="col-12 py-3">
                <table class="table table-bordered bg-white table-striped">
                    <thead>
                        <tr>
                            <td class="text-end">ImageID</td>
                            <td>MemberID</td>
                            <td>Member's Profile Image</td>
                            <td>Member's Cover Image</td>
                            <td class="text-center">Edit</td>
                        </tr>
                    </thead>
                    <tbody id="Images_Table">
                        <?php

                        $allImg = $db->getDatas("SELECT * FROM images");

                        foreach ($allImg as $img) {
                            $imgid = $img->imgID;
                            if (empty($img->Member_Profileimg)) {
                                $img->Member_Profileimg = "null";
                            }
                            if (empty($img->Member_Coverimg)) {
                                $img->Member_Coverimg = "null";
                            }
                        ?>

                            <tr id="image_info_<?= $imgid ?>">
                                <form id="imagesformid_<?= $imgid ?>" method="post">
                                    <td class="text-end"><?= $imgid ?></td>
                                    <td><?= $img->MemberID ?></td>
                                    <td>
                                        <span class="span_<?= $imgid ?>" id="span_Profileimg_<?= $imgid ?>"><?= $img->Member_Profileimg ?></span>
                                        <input type="text" name="form_profileimg" value="<?= $img->Member_Profileimg ?>" class="form-control form-control-sm d-none input_<?= $imgid ?>">
                                    </td>
                                    <td>
                                        <span class="span_<?= $imgid ?>" id="span_Coverimg_<?= $imgid ?>"><?= $img->Member_Coverimg ?></span>
                                        <input type="text" name="form_coverimg" value="<?= $img->Member_Coverimg ?>" class="form-control form-control-sm d-none input_<?= $imgid ?>">
                                    </td>
                                    <td class="text-center" id="edit_<?= $imgid ?>">
                                        <i class="fas fa-edit editItem" id="editimg_<?= $imgid ?>" onClick="openeditimg('<?= $imgid ?>')"></i>
                                        <button type="button" class="btn btn-sm btn-outline-dark d-none editbtn_<?= $imgid ?>" onClick="editImage('<?= $imgid ?>','imagesformid_<?= $imgid ?>')"><?= $translates["save"] ?><span class="spinner" id="images_spinner"></span></button>
                                    </td>
                                </form>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function openeditimg(imgid) {
        $('.input_' + imgid).removeClass("d-none");
        $('.input_' + imgid).addClass("d-inline-table");
        $('.span_' + imgid).removeClass("d-inline-table");
        $('.span_' + imgid).addClass("d-none");
        $('#editimg_' + imgid).addClass("d-none");
        $('.editbtn_' + imgid).removeClass("d-none");
        $('.editbtn_' + imgid).addClass('d-inline-table');
    }
</script>