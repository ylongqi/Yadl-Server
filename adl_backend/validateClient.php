<?php

function validateClient() {

    if (isset($_POST["client_id"])) {
        $gt = clientVerifyAPI::getSessionByClientid(md5($_POST["client_id"]));
        if (strcmp($gt["session"], $_SESSION["state"]) == 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getUTCtimeFraction(){
    
    list($usec, $sec) = explode(' ', microtime());
    $usec = str_replace("0.", ".", $usec);
    
    return date('Y-m-d H:i:s', $sec).substr($usec, 0, 3);
}
