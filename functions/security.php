<?php

function security($text){
    if(isset($_POST[$text]) or isset($_GET[$text])){
        if(!empty($_POST[$text])){
            $text = trim($_POST[$text]);
        }else{
            $text = trim($_GET[$text]);
        }
        $text = stripslashes($text);
        $text = strip_tags($text);
        $text = htmlspecialchars($text,ENT_QUOTES);
        $text = str_replace("insert","",$text); 
        $text = str_replace("INSERT","",$text); 
        $text = str_replace("select","",$text); 
        $text = str_replace("SELECT","",$text); 
        $text = str_replace("exec","",$text); 
        $text = str_replace("EXEC","",$text); 
        $text = str_replace("union","",$text); 
        $text = str_replace("UNION","",$text); 
        $text = str_replace("drop","",$text); 
        $text = str_replace("DROP","",$text); 
        // $text = htmlentities($text);
        return $text;
    }
}

?>