<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('dbManage/db_Connect.php');

class clientVerifyAPI{
    
    public static function storeClientIndex($clientid, $session){
        
        $db = db_Connect();
	$result = $db -> query("insert into clientVerify values('" . $clientid . "', '". $session. "')");

	if(!$result){
		throw new Exception("Database Error!");
	}
    }
    
    public static function getSessionByClientid($clientid){
        
        $db = db_Connect();
	$result = $db -> query("select session from clientVerify where clientid = '".$clientid."'");

	if(!$result){
            throw new Exception("Database Error!");
	} else {
            return $result -> fetch(PDO::FETCH_ASSOC);
        }
    }
}