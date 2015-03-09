<?php

function db_Connect(){
    
    //$db = new mysqli('localhost', 'yadamanager', 'yada', 'adl');

    $db = new PDO('mysql:host=localhost; dbname=adl; charset=utf8', "yadamanager", "yada");
    
    if (!$db) {
        throw new Exception('Database Connect Failed!');
    } else {
        return $db;
    }
}

