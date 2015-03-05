<?php

session_start();

require_once('dbManage/clientVerifyAPI.php');

if(isset($_POST["client_id"])){
    $session = md5(rand());
    clientVerifyAPI::storeClientIndex(md5($_POST["client_id"]), $session);
    $_SESSION["state"] = $session;
}