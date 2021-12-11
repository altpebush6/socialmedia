<?php
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('Europe/Istanbul');

require_once "functions/time.php";
require_once "functions/routing.php";
require_once "functions/security.php";
require_once "classes/AllClasses.php";

if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) or strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != 'xmlhttprequest') {
  header("Location: http://localhost/aybu/socialmedia//404.php");
}

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
$postid = $_GET['PostID'];

$result = array();

switch ($operation) {
  case 'removePostRep':
    $deleteReport = $db->Delete("DELETE FROM reports_posts WHERE ReportedID = ?", array($postid));
    $deletepostsReport = $db->Update("UPDATE posts SET PostReport = ? WHERE PostID = ?", array(0, $postid));
    echo "success";
    break;

  case 'removeCommentRep':
    $deleteReport = $db->Delete("DELETE FROM reports_comments WHERE ReportedPostID = ?", array($postid));
    $deletepostsReport = $db->Update("UPDATE posts SET CommentReport = ? WHERE PostID = ?", array(0, $postid));
    $deletefrompostcomments = $db->Update("UPDATE postcomments SET CommentReport = ? WHERE PostID = ?", array(0, $postid));
    echo "success";
    break;


  case 'deletepost':
    $deletepost = $db->Update("UPDATE posts SET PostActive = ?, DeletedBy = ? WHERE PostID = ?", array(0, $AdminID, $postid));
    $deletecomments = $db->Update("UPDATE postcomments SET CommentActive = ? WHERE PostID = ?", array(0, $postid));
    $deletelikes = $db->Delete("DELETE FROM postlike WHERE PostID = ?", array($postid));
    $topic = $db->getColumnData("SELECT PostTopic FROM posts WHERE PostID = ?", array($postid));
    $gettopicCount = $db->getColumnData("SELECT TopicInteraction FROM topics WHERE TopicLink = ?", array($topic));
    $newCount = $gettopicCount - 1;
    $addtopicCount = $db->Update("UPDATE topics SET TopicInteraction = ? WHERE TopicLink = ?", array($newCount, $topic));
    echo $postid;
    break;

  case 'deleteComment':
    $commentid = $_GET['CommentID'];
    $postid = $db->getColumnData("SELECT PostID FROM postcomments WHERE CommentID = ?", array($commentid));
    $db->Update("UPDATE postcomments SET CommentActive = ?, DeletedBy = ?  WHERE CommentID = ?", array(0, $AdminID, $commentid));
    $count_comment = $db->getColumnData("SELECT COUNT(*) FROM postcomments WHERE PostID = ? AND CommentActive = ?", array($postid, 1));
    echo $count_comment;
    break;
}
