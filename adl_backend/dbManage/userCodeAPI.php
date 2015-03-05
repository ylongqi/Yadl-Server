<?php

require_once 'db_Connect.php';

class userCodeAPI{
    
    public static function insert($userid, $code){
        
        $db = db_Connect();
	$result = $db -> query("insert into userCode values('" . $userid . "', '". $code. "')");
        
        if(!$result){
            throw new Exception("Database Insert Error!");
        }

    }
    
    public static function exist_user($userid){
        
        $db = db_Connect();
        $result = $db -> query("select * from userCode where userid = '$userid'");
        
        if($result ->rowCount() >= 1){
            return true;
        } else {
            return false;
        }
    }
    
    public static function exist_code($code){
        
        $db = db_Connect();
        $result = $db -> query("select * from userCode where code = '$code'");
        
        if($result ->rowCount() >= 1){
            return true;
        } else {
            return false;
        }
    }
    
    public static function getUserByCode($code){
        
        $db = db_Connect();
        $result = $db -> query("select userid from userCode where code = '$code'");
        
        if(!$result){
            throw new Exception("userCode Select Error!");
        } else {
            return $result -> fetch(PDO::FETCH_ASSOC);
        }
    }
}

