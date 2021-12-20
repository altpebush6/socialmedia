<?php
namespace aybu\token;
use aybu\session\session as session;

class token extends session{
    public static function createToken(){
        return parent::create("PHPToken",md5(uniqid(mt_rand())));
    }
    public static function control($token){
        if(parent::isHave("PHPToken") and $token == parent::get("PHPToken")){
            return true;
        }else{
            return false;
        }
    }
}

?>