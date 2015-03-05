<?php

require_once 'API.V1.Class.php';

try{
    $YadlAPI = new YADLAPI($_REQUEST['request']);
    $echo_string = $YadlAPI -> processAPI();
    
    if($echo_string){
        echo $echo_string;
    }
} catch (Exception $ex) {
    echo json_encode(Array('error' => $e->getMessage()));
}
