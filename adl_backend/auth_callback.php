<?php
use OmhStack\OmhAuth as OmhAuth;
use dbManage\OmhAuthAPI as OmhAuthTable;
session_start();

require_once("OmhStack/OmhAuth.php");
require_once("dbManage/OmhAuthAPI.php");
#Auto loading authorization code from Ohmage Server
/*
function __autoload($class){
    $class = str_replace('\\', '/', $class).'.php';
    require_once($class);
}
 * 
 */

#Callback URL: http://yadl.yadagame.com/callback?code={OAUTH_CODE}
#Or if sign-in Failed: http://yadl.yadagame.com/callback?error={ERROR_MSG}

if(isset($_GET["code"])){
    $code = $_GET["code"];
    $token = OmhAuth::get_access_token($code);
    if(isset($token["access_token"])){
        $auth_code = OmhAuth::check_valide_token($token["access_token"]);

        if(OmhAuthTable::checkCodeExist($auth_code)){
            OmhAuthTable::updateAccessToken($auth_code, $token["access_token"]);
        } else {
            OmhAuthTable::initStoreToken($auth_code, $token["access_token"], $token["refresh_token"], md5($auth_code));
        }

        $_SESSION["user_id"] = md5($auth_code);
        header('Location: index.html');
    }
} else {
    echo OmhAuth::redirect_address();
}

