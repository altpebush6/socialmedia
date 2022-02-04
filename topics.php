<div class="row">
    <div class="col-12 border bg-light p-4">
        <div class="row">
            <div class="row">
                <div class="col-12 text-center">
                    <h2>All Topics</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6 mx-auto p-3">
                    <div class="row justify-content-center">
                        <div class="col-10 p-0 ps-3 text-center"><input type="text" id="new_topic" maxlength="50" name="new_topic" class="form-control" placeholder="Enter a new topic"></div>
                        <div class="col-2 p-0 text-center"><button type="button" class="btn btn-outline-dark" id="addTopic" onClick="addTopic()">Add<span class="spinner" id="topic_spinner"></span></button></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6 p-3 mx-auto">
                    <ul class="list-group" id="topics_ul" style="max-height:50vh;overflow-y:auto;">
                        <?php
                        $topics = $db->getDatas("SELECT * FROM topics WHERE TopicActive = ? ORDER BY TopicOrder ASC", array(1));
                        $order = 1;
                        foreach ($topics as $topic) {
                        ?>
                            <li class="list-group-item d-flex flex-row justify-content-between fs-5 topic-item topic_<?= $topic->TopicID ?>" id="order-<?= $topic->TopicID ?>">
                                <span><?= $topic->TopicName . " (" . $topic->TopicInteraction . ")" ?></span>
                                <span>#<?= $order ?> <i class="fas fa-trash-alt deletetopic" onClick="deleteTopic('<?= $topic->TopicID ?>')"></i></span>
                            </li>
                        <?php
                            $order += 1;
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>