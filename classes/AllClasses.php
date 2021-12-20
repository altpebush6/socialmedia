<?php

spl_autoload_register(function($classname){

$prefix = "aybu\\"; // aybu\

$basedir = __DIR__."/"; // C:\wamp64\www\aybu\socialmedia\classes/

$length = strlen($prefix); // 5

if(strncmp($prefix,$classname,$length) !== 0){
    return;
}
$classname= strtolower($classname); // aybu/db/DB -> aybu/db/db

$relative_class = substr($classname,$length); // db/db

$path = $basedir.str_replace("\\","/",$relative_class).".php"; // C:/wamp64/www/aybu/socialmedia/classes/db/db.php


if(file_exists($path)){
    require_once $path;
}


})

?>