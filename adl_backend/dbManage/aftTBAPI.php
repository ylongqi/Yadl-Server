<?php

require_once('dbManage/db_Connect.php');

class aftTBAPI {
    
    public static function insert($mainIndex, $userid, $time){
        
        $db = db_Connect();
        $flag = $db -> query("insert into aftTB values ('" . $mainIndex . "', '" . $userid . "', '" . $time . "')");
        
        if(!$flag){
            throw new Exception("Database Insert Error!");
        }
    }
    
    public static function exist_user($userid){
        
        $db = db_Connect();    
        $result = $db -> query("select * from aftTB where userid = '".$userid."'");
        
        $rowNum = $result -> rowCount();
        
        if($rowNum >= 1){
            return true;
        } else {
            return false;
        }
        
    }
    
    public static function getIndexBySpan($userid, $span){
        
        $db = db_Connect();
        $result = $db -> query("select mainIndex, time from aftTB where userid = '$userid' and (time between NOW() - INTERVAL $span DAY and NOW() + INTERVAL 1 DAY)");
        
        if(!$result){
            throw new Exception("Database Select Error!");
        } else {
            return $result ->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

