<?php
namespace dbManage;
use \PDO as PDO;

require_once('dbManage/db_Connect.php');

class OmhAuthAPI{
    
    public static function validate_md5($md5){
        
        $db = db_Connect();
        $result = $db->query("select * from omhAuth where md5 = '" . $md5 . "'");
        $rowNum = $result-> rowCount();

        if ($rowNum >= 1) {
            return true;
        } else {
            return false;
        }
        
    }
    
    public static function getCodeFromMd5($md5){
        
        $db = db_Connect();
        $result = $db->query("select * from omhAuth where md5 = '" . $md5 . "'");
        $row = $result -> fetch(PDO::FETCH_ASSOC);
        return $row["code"];
        
    }
    
    public static function initStoreToken($code, $access_token, $refresh_token, $md5){
        
        $db = db_Connect();
	$result = $db -> query("insert into omhAuth values('" . $code . "', '". $access_token. "', '". $refresh_token. "', '". $md5. "')");

	if(!$result){
            throw new Exception("Database Error!");
	}
    }
    
    public static function checkCodeExist($code){
        
        $db = db_Connect();
        $result = $db->query("select * from omhAuth where code = '" . $code . "'");
        $rowNum = $result-> rowCount();

        if ($rowNum >= 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getToken($code){
        
        $db = db_Connect();
        $result = $db->query("select * from omhAuth where code = '" . $code . "'");
        return $result -> fetch(PDO::FETCH_ASSOC);
    }
    
    public static function updateAccessToken($code, $access_token){
        
        $db = db_Connect();
	$result = $db -> query("update omhAuth set access_token = '".$access_token."' where code = '".$code."'");

	if(!$result){
            throw new Exception("Database Error!");
	}
        
    }
}

