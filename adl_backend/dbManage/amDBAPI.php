<?php

require_once('dbManage/db_Connect.php');

class amDBAPI{
    
    private static $num = 27;
    
    public static function getWordByIndex($index){
        
        $db = db_Connect();
        
        $result = $db -> query("select word from amDB where dataIndex = '".$index."'");
        
        if(!$result){
            throw new Exception("Database Error!");
	} else {
            return $result -> fetch(PDO::FETCH_ASSOC);
        }
        
    }
    
    public static function getReferenceByIndex($index){
        
        $db = db_Connect();
        $result = $db -> query("select reference from amDB where dataIndex = '".$index."'");

        if(!$result){
            throw new Exception("Database Error!");
        } else {
            return $result -> fetch(PDO::FETCH_ASSOC);
        }
    }
    
    public static function insert($index, $word, $reference){
        
        $db = db_Connect();
	$result = $db -> query("insert into amDB values('" . $index . "', '". $word. "', '". $reference. "')");
        
        if(!$result){
            throw new Exception("Database Insert Error!");
        }

    }
    
    public static function getAllImages(){
        
        $db = db_Connect();
        $result = $db -> query("select dataIndex, reference from amDB order by dataIndex limit ".(amDBAPI::$num));
        
        if(!$result){
            throw new Exception("Database Error!");
        } else {
            return $result -> fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    public static function getAllImagesIndexWord(){
        
        $db = db_Connect();
        $result = $db -> query("select dataIndex as img, word as text from amDB order by dataIndex ASC limit ".(amDBAPI::$num));
        
        if(!$result){
            throw new Exception("Database Error!");
        } else {
            return $result -> fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    public static function getDatasetSize(){
        return amDBAPI::$num;
    }
    
}