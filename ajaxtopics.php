<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once "functions/seolink.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";

if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) or strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != 'xmlhttprequest') {
    header("Location: http://localhost/aybu/adminpanel/404.php");
}

$db = new aybu\db\mysqlDB();

$SS = new aybu\session\session();

$AdminID = $SS->get("AdminID");

if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
} else {
    $language = "tr";
}
require_once "languages/language_" . $language . ".php";

$result = array();

$Operation = $_GET["Operation"];

switch ($Operation) {
    case 'addTopic':
        $TopicName = security("topicName");
        if (!empty($TopicName)) {
            $seo = seolink(strtolower($TopicName));

            $countTopics = $db->getColumnData("SELECT COUNT(*) FROM topics WHERE TopicActive = ?", array(1));

            $addTopic = $db->Insert("INSERT INTO topics SET TopicName = ?, TopicLink = ?, TopicActive = ?, TopicOrder = ?, AdminID = ?", array($TopicName, $seo, 1, ($countTopics + 1), $AdminID));

            $result["newTopic"] = '<li class="list-group-item d-flex flex-row justify-content-between fs-5 topic-item topic_' . $addTopic . '" id="order-' . $addTopic . '">
                                        <span>' . $TopicName . ' (0)</span>
                                        <span>#' . ($countTopics + 1) . ' <i class="fas fa-trash-alt deletetopic" onClick=\'deleteTopic("' . $addTopic . '")\'></i></span>
                                    </li>';
        }
        echo json_encode($result);
        break;

    case 'delTopic':
        $topicID = security("TopicID");
        $deltopic = $db->Update("UPDATE topics SET TopicActive = ? WHERE TopicID = ?", array(0, $topicID));
        $result["success"] = "ok";
        echo json_encode($result);
        break;

    case 'sortTopics':
        $list = security("list");
        $deletethese = "/amp;/";
        $list = preg_replace($deletethese, "", $list);
        $output = array();
        parse_str($list, $output);
        $response = $output["order"];
        foreach ($response as $order => $item) {
            $db->Update("UPDATE topics SET TopicOrder = ? WHERE TopicID = ?", array(($order + 1), $item));
        }

        $topics = $db->getDatas("SELECT * FROM topics WHERE TopicActive = ? ORDER BY TopicOrder ASC", array(1));
        $order = 1;
        $result = '';
        foreach ($topics as $topic) {
            $result .= '<li class="list-group-item d-flex flex-row justify-content-between fs-5 topic-item topic_' . $topic->TopicID . '" id="order-' . $topic->TopicID . '">
                            <span>' . $topic->TopicName . ' (' . $topic->TopicInteraction . ')</span>
                            <span>#' . ($order) . ' <i class="fas fa-trash-alt deletetopic" onClick=\'deleteTopic("' . $topic->TopicID . '")\'></i></span>
                         </li>';
            $order += 1;
        }
        echo $result;
        break;
}
