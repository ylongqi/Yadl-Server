<?php
use dbManage\OmhAuthAPI as OmhAuthTable;
session_start();
require_once('dbManage/clientVerifyAPI.php');

#Auto loading authorization code from Ohmage Server
function __autoload($class){
    $class = str_replace('\\', '/', $class).'.php';
    require_once($class);
}

if(isset($_POST["client_id"])){
    $session = md5(rand());
    clientVerifyAPI::storeClientIndex(md5($_POST["client_id"]), $session);
    $_SESSION["state"] = $session;
    
    if(isset($_SESSION["user_id"]) && OmhAuthTable::validate_md5($_SESSION["user_id"])){
        echo "true";
    } else {
        echo "false";
    }
}
