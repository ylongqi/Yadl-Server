<?php

require_once('dbManage/db_Connect.php');

class aisTBAPI {

    public static function batchInsert($userid, $reference) {

        $db = db_Connect();
        $sql_str = "insert into aisTB values (1, '" . $userid . "', '" . $reference[0]["dataIndex"] ."', '" . $reference[0]["reference"] . "')";

        for ($i = 1; $i < sizeof($reference); $i ++) {
            $sql_str = $sql_str . ", (" . ($i + 1) . ", '" . $userid . "', '" . $reference[$i]["dataIndex"] . "', '" . $reference[$i]["reference"]. "')";
        }

        $flag = $db->query($sql_str);

        if (!$flag) {
            throw new Exception("Database Insert Error!");
        }
    }
    
    public static function getreferenceByseqNum($seqNum){
        
        $db = db_Connect();
        $result = $db->query("select reference from aisTB where seqNum = '".$seqNum."'");
        
        if(!$result){
            throw new Exception("Database Error!");
        } else {
            return $result -> fetch(PDO::FETCH_ASSOC);
        }
    }
    
    public static function getdataIndexByseqNum($seqNum){
        $db = db_Connect();
        $result = $db->query("select dataIndex from aisTB where seqNum = ".$seqNum);
        
        if(!$result){
            throw new Exception("Database Error!");
        } else{
            return $result -> fetch(PDO::FETCH_ASSOC);
        }
    }

    public static function getSizeByUserid($userid){
        
        $db = db_Connect();
        $result = $db->query("select count(*) as co from aisTB where userid = '" . $userid . "'");
        
        if(!$result){
            throw new Exception("Database Error!");
        } else {
            $row = $result -> fetch(PDO::FETCH_ASSOC);
            return $row["co"];
        }
    }
    
    public static function exist_user($userid) {

        $db = db_Connect();
        $result = $db->query("select * from aisTB where userid = '" . $userid . "'");
        $rowNum = $result-> rowCount();

        if ($rowNum >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function clear_user($userid) {

        if (aisTBAPI::exist_user($userid)) {
            $db = db_Connect();
            $flag = $db->query("delete from aisTB where userid = '" . $userid . "'");

            if (!$flag) {
                throw new Exception("Database Error!");
            }
        }
    }

}
