<?php

namespace aybu\session;

class session{

    public static function create($name,$value){
        return $_SESSION[$name]=$value;
    }
    public static function isHave($name){
        if(isset($_SESSION[$name])){
            return true;
        }else{
            return false;
        }
    }
    public static function get($name){
        if(self::isHave($name)){
            return $_SESSION[$name];
        }else{
            return false;
        }
    }
    public static function del($name){
        if(self::isHave($name)){
            unset($_SESSION[$name]);
        }
    }
    public static function delAll(){
        session_destroy();
    }
}



?>