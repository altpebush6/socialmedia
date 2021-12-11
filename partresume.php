<div class="row mt-4">
    <div class="col-12">
        <table class=" table table-light table-hover">
            <thead>
                <th></th>
                <th class="fs-4 p-3 text-center"><?= $translates["cvset"] ?></th>
                <th></th>
            </thead>
            <?php
            $list = $db->getDatas("SELECT * FROM nav_resume_$language");
            foreach ($list as $items) {
                $contents_resume = $db->getColumnData("SELECT $items->NavDB FROM memberresume WHERE MemberID = $memberid ");
                $contents_resume = ($contents_resume == '' ? $translates["undefined"] : $contents_resume);
                if ($items->NavID == 7) {
                    $alljobs = $db->getColumnData("SELECT JobExperiments FROM memberresume WHERE MemberID = ?", array($memberid));
                    $alljobs = explode(",", $alljobs);
                    if (count($alljobs) < 3) {
                        $contents_resume = preg_replace("/,/"," ", $contents_resume);
                    }
                }
            ?>
                <tr class="text-center border-bottom" id="<?= $items->NavForm ?>">
                    <div class="row">
                        <th class="py-3 col-3 border-end"><?= $items->NavName ?></th>
                        <td class="py-3 col-6" id="contents_resume_<?= $items->NavForm ?>"><?= $contents_resume ?></td>
                        <td class="edit-info py-3 col-3 border-start" onClick="EditOpen('<?= $items->NavForm ?>')"><?= $translates["edit"] ?> <i class="fas fa-pen" style="font-size:14px"></i></td>
                    </div>
                </tr>
                <tr class="text-center border-bottom d-none" id="edit_<?= $items->NavForm ?>">
                    <form method="post" id="form_<?= $items->NavForm ?>">
                        <div class="row">
                            <th class="py-3 col-3 border-end"><?= $items->NavName ?></th>
                            <td class="py-3 col-6">
                                <?php if ($items->NavID == 7) { ?>
                                    <div class="row flex-column w-100">
                                        <div class="col-12 p-0 mx-auto">
                                            <div class="input-group w-75 mx-auto d-flex flex-row justify-content-center">
                                                <input type="text" id="<?= $items->NavForm ?>_input" maxlength="255" class="form-control w-50" name="<?= $items->NavForm ?>" placeholder="<?= $items->NavName ?>">
                                                <select class="form-select ms-1 w-25" name="job_year" id="job_year">
                                                    <?php
                                                    for ($i = 1; $i <= 10; $i++) { ?>
                                                        <option value="<?= $i ?>"><?= $i ?> <?= $translates["year"] ?></option>
                                                    <?php } ?>
                                                </select>
                                                <button type="button" class="btn btn-sm ms-2 rounded-3 btn-primary w-20" id="addJob"><?= $translates["add"] ?></button>

                                            </div>
                                        </div>
                                        <?php $alljobs = $db->getColumnData("SELECT JobExperiments FROM memberresume WHERE MemberID = ?", array($memberid)); ?>
                                        <div class="col-9 mx-auto mt-1 text-start" id="added_jobs" jobs="<?= $alljobs ?>">
                                            <?php
                                            $alljobs = explode(",", $alljobs);
                                            foreach ($alljobs as $jobID => $job) {
                                                if (!empty($job)) {
                                            ?>
                                                    <span class="btn btn-primary btn-sm m-1" id="job_<?= $jobID ?>" style="font-size: 13px;">
                                                        <span><?= $job ?> </span>
                                                        <button type="button" class="btn-close" jobname="<?= $job ?>" jobid="<?= $jobID ?>" job="<?= $job ?>" style="font-size:9px;"></button>
                                                    </span>
                                            <?php }
                                            } ?>
                                        </div>
                                    </div>

                                <?php } else { ?>
                                    <div class="w-70 mx-auto">
                                        <input type="text" id="<?= $items->NavForm ?>_input" class="form-control form-control-sm" name="<?= $items->NavForm ?>" placeholder="<?= $items->NavName ?>">
                                    </div>
                                <?php } ?>
                            </td>
                            <td class="py-3 col-3 border-start"><button type="button" class="btn btn-sm btn-outline-primary submitresume" onClick='SendFormResume("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavForm ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavForm ?>_spinner"></span></button></td>
                        </div>
                    </form>
                </tr>
            <?php } ?>
        </table>
        <div id="resume_result"></div>
    </div>
</div>

<script>
    $(function() {
        var JobID = 0;
        $("#j_exp_input").on("keypress", function(e) {
            var personJob = $(this).val();
            var JobYear = $("#job_year").val();
            if (e.which == 13) {
                e.preventDefault();
                if (personJob != "") {
                    JobID += 1;
                    $("#added_jobs").append('<span class="btn btn-primary btn-sm m-1" id="job_' + JobID + '"  style="font-size: 13px;"><span>' + personJob + ' (' + JobYear + ' <?= $translates["year"] ?>) </span><button type="button" class="btn-close" jobname="' + personJob + '(' + JobYear + ' <?= $translates["year"] ?>)," jobid="' + JobID + '" style="font-size:9px;"></button></span>');
                    var pre_attr = $("#added_jobs").attr("jobs");
                    $("#added_jobs").attr("jobs", pre_attr + personJob + ' (' + JobYear + ' <?= $translates["year"] ?>), ');
                    $(this).html("");
                    $(this).val("");
                }
            }
        });
        $("#addJob").on("click", function() {
            var personJob = $("#j_exp_input").val();
            var JobYear = $("#job_year").val();
            if (personJob != "") {
                JobID += 1;
                $("#added_jobs").append('<span class="btn btn-primary btn-sm m-1" id="job_' + JobID + '"  style="font-size: 13px;"><span>' + personJob + ' (' + JobYear + ' <?= $translates["year"] ?>) </span><button type="button" class="btn-close" jobname="' + personJob + '(' + JobYear + ' <?= $translates["year"] ?>)," jobid="' + JobID + '" style="font-size:9px;"></button></span>');
                var pre_attr = $("#added_jobs").attr("jobs");
                $("#added_jobs").attr("jobs", pre_attr + personJob + ' (' + JobYear + ' <?= $translates["year"] ?>), ');
                $("#j_exp_input").html("");
                $("#j_exp_input").val("");
            }
        });
        $("#added_jobs").on("click", ".btn-close", function() {
            var pre_attr = $("#added_jobs").attr("jobs");
            var JobID = $(this).attr("jobid");
            var JobName = $(this).attr("jobname");
            $.ajax({
                type: "post",
                url: "http://localhost/aybu/socialmedia/ajaxsettings.php?operation=removeJob",
                data: {
                    "Jobs": pre_attr,
                    "RemoveJob": JobName
                },
                dataType: "json",
                success: function(result) {
                    $("#added_jobs").attr("jobs", result.success);
                }
            });
            var JobID = $(this).attr("jobid");
            $("#job_" + JobID).remove();
        });
    });
</script>