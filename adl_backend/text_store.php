<?php

session_start();

require_once('dbManage/afTBAPI.php');
require_once('dbManage/amDBAPI.php');
require_once('dbManage/clientVerifyAPI.php');
require_once('validateClient.php');

use dbManage\OmhAuthAPI as OmhAuthTable;
use OmhStack\OmhAuth as OmhAuth;

#Auto loading authorization code from Ohmage Server
function __autoload($class){
    $class = str_replace('\\', '/', $class).'.php';
    require_once($class);
}

$user_id = OmhAuthTable::getCodeFromMd5($_SESSION["user_id"]);

if (validateClient()) {
    $current_time = getUTCtimeFraction();
    $dataIndex = uniqid("wrd_");
    afTBAPI::insert($_SESSION['mainIndex'], $dataIndex, $user_id, 2, $current_time);
    amDBAPI::insert($dataIndex, $_POST['word'], $_POST['word']);
    
    #Sending personalized acitivty words to Ohmage-omh Server
    #Body Data:
    #---- activity: Description
    #---- type: "word"
    $body_data["activity"] = $_POST["word"];
    $body_data["type"] = "word";
    $token = OmhAuthTable::getToken($user_id);
    OmhAuth::send_datapoint($token["access_token"], $body_data);
}

