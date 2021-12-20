<?php
function calculateTime($gettime)
{
  date_default_timezone_set('Europe/Istanbul');

  $SS = new aybu\session\session();
  if ($SS->isHave("Language")) {
    $language = $SS->get("Language");
  } else {
    $language = "tr";
  }
  switch ($language) {
    case 'tr':
      $day = "g";
      $hour = "s";
      $min = "dk";
      $sec = "sn";
      $now = " az önce";
      break;
    case 'en':
      $day = "d";
      $hour = "h";
      $min = "min";
      $sec = "sec";
      $now = " now";
      break;
  }

  $now_time = date("Y-m-d H:i:s");
  $strt = strtotime($gettime);
  $fnsh = strtotime($now_time);
  $diff = abs($fnsh - $strt);

  if ($diff > 86400) {
    $diff = $diff / 86400;
    $diff = intval($diff);
    $diff = strval($diff) . $day;
    return $diff;
  } elseif ($diff > 3600) {
    $diff = $diff / 3600;
    $diff = intval($diff);
    $diff = strval($diff) . $hour;
    return $diff;
  } elseif ($diff > 60) {
    $diff = $diff / 60;
    $diff = intval($diff);
    $diff = strval($diff) . $min;
    return $diff;
  } elseif ($diff < 10) {
    $diff = intval($diff);
    $diff = $now;
    return $diff;
  } else {
    $diff = intval($diff);
    $diff = strval($diff) . $sec;
    return $diff;
  }
}
function messageTime($dateTime)
{
  $explosion = explode(" ", $dateTime);
  $time = $explosion[1];
  $explosion2 = explode(":", $time);
  $hour = $explosion2[0];
  $minute = $explosion2[1];
  return $hour . ":" . $minute;
}

function TimeDiff($time)
{
  $nowTime = time();
  $timeDiff = $nowTime - $time;
  return $timeDiff;
}

function TimeStampTranslater($time)
{
  $nowTime = time();
  $time = strtotime($time);
  $timeDiffer = $nowTime - $time;
  return $timeDiffer;
}

function myDate($dates)
{
  $explosion = explode(" ", $dates);
  $date = $explosion[0];
  $explosion2 = explode(".", $date);
  $day = $explosion2[2];
  $month = $explosion2[1];
  $year = $explosion2[0];
  return $day . "." . $month . "." . $year;
}
