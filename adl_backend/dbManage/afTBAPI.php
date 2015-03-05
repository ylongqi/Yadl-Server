<?php

require_once('dbManage/db_Connect.php');

class afTBAPI{
    
    public static function insert($mainIndex, $dataIndex, $userid, $choice, $time){
        
        $db = db_Connect();
        $flag = $db -> query("insert into afTB values ('".$mainIndex."', '".$dataIndex."', '".$userid."', '".$choice."', '".$time."')");
        
        if(!$flag){
		throw new Exception("Database Insert Error!");
	}
        
    }
    
    public static function getActivityByUserid($userid){
        
        $db = db_Connect();
        $result = $db -> query("select amDB.dataIndex as dataIndex, amDB.reference as reference from (".
                                "( select dataIndex, sum(choice) as su from afTB where userid = '".$userid."' group by dataIndex) as tempTB ".
                                "left join amDB on tempTB.dataIndex = amDB.dataIndex )");
        
        if(!$result){
            throw new Exception("Database Error!");
        } else {
            return $result -> fetchAll(PDO::FETCH_ASSOC);
        }
        
    }
    
    public static function getCurrentHardActivity($mainIndex){
        
        $db = db_Connect();
        $result = $db -> query("select amDB.reference as reference from (".
                            "select dataIndex from afTB where mainIndex = '".$mainIndex."' and choice = 2 ) as tempTB ".
                            "left join amDB on tempTB.dataIndex = amDB.dataIndex");
        
        if(!$result){
            throw new Exception("Database Error!");
        } else {
            return $result->fetchAll(PDO::FETCH_COLUMN, 0);
        }
    }
    
    public static function getActivityWordBymainIndex($mainIndex, $choice){
        
        $db = db_Connect();
        $result = $db -> query("select amDB.word as word from (".
                            "select dataIndex from afTB where mainIndex = '".$mainIndex."' and choice = '$choice' ) as tempTB ".
                            "left join amDB on tempTB.dataIndex = amDB.dataIndex");
        
        if(!$result){
            throw new Exception("Database Error!");
        } else {
            return $result->fetchAll(PDO::FETCH_COLUMN, 0);
        }
    }
    
    public static function exist_user($userid){
        
        $db = db_Connect();    
        $result = $db -> query("select time from afTB where userid = '".$userid."'");
        $rowNum = $result -> num_rows;
        
        if($rowNum >= 1){
            return true;
        } else {
            return false;
        }
    }
}


