<?php

/*
 * 
 * Uploading Parameters:
 * 
 * 'user': email account (identity)
 * 'img': image number (picNum)
 * 'src': image source
 * 'choice': selection (1: easy 2: hard)
 * 'time': time of selection
 * 
 * Echoing JSON Parameters:
 * 
 * previous_image_number: 
 * previous_image_src:
 * total_image_number:
 * ==============
 * image_number:
 * image_src: 
 * ==============
 * OR
 * ==============
 * hard_list:
 * ==============
 * 
 * 
 */

session_start();

require_once('dbManage/afTBAPI.php');
require_once('dbManage/aftTBAPI.php');
require_once('dbManage/aisTBAPI.php');
require_once('dbManage/amDBAPI.php');
require_once('dbManage/clientVerifyAPI.php');
require_once('dbManage/userCodeAPI.php');
require_once('validateClient.php');

#Auto loading authorization code from Ohmage Server
function __autoload($class){
    $class = str_replace('\\', '/', $class).'.php';
    require_once($class);
}

use dbManage\OmhAuthAPI as OmhAuthTable;
use OmhStack\OmhAuth as OmhAuth;

$img_feedback = 1;

if (validateClient()) {

    //$engine_instance = new engine();
    $adl_echo = array();
    $current_image_number = 0;
    $current_time = getUTCtimeFraction();
    $user_id = OmhAuthTable::getCodeFromMd5($_SESSION["user_id"]);

    if (isset($_POST['choice'])) {
       
        $database_size = aisTBAPI::getSizeByUserid($user_id);
        #Get data index of current activity image: stored in $current_dataIndex
        $current_dataIndex = aisTBAPI::getdataIndexByseqNum($_POST["img"]);
        afTBAPI::insert($_SESSION["mainIndex"], $current_dataIndex["dataIndex"], $user_id, $_POST['choice'], $current_time);
        $current_image_number = intval($_POST["img"]);

        #Sending Hard activity info to Ohmage-omh Server
        #Body Data: 
        #---- activity: Description
        #---- type: "image"
        #---- source: URL
        if($_POST['choice'] == 2){
            $body_data = array();
            $body_data["activity"] = amDBAPI::getWordByIndex($current_dataIndex);
            $body_data["type"] = "image";
            $body_data["source"] = amDBAPI::getReferenceByIndex($current_dataIndex);
            $token = OmhAuthTable::getToken($user_id);
            OmhAuth::send_datapoint($token["access_token"], $body_data);
        }
        
        #Handling hard activity list (Process)
        if ($current_image_number <= $database_size) {
            if ($_POST['choice'] == 2) {
                $adl_echo['previous_image_src'] = $_POST['src'];
            }
        }

        if ($current_image_number == $database_size) {
            $img_feedback = 0;
            $adl_echo["hard_list"] = afTBAPI::getCurrentHardActivity($_SESSION["mainIndex"]);
            aftTBAPI::insert($_SESSION["mainIndex"], $user_id, $current_time);
            aisTBAPI::clear_user($user_id);
        }
    } else {
        if(!userCodeAPI::exist_user($user_id)){
            $code = md5(uniqid("cod_"));
            userCodeAPI::insert($user_id, $code);
        }
        
        aisTBAPI::clear_user($user_id);
        if(aftTBAPI::exist_user($user_id)){
            $reference = afTBAPI::getActivityByUserid($user_id);
        } else {
            $reference = amDBAPI::getAllImages();
        }
        shuffle($reference);
        aisTBAPI::batchInsert($user_id, $reference);
        $adl_echo["total_image_number"] = sizeof($reference);
        $_SESSION["mainIndex"] = uniqid("mid_");
    }

    if ($img_feedback == 1) {
        $data_source = aisTBAPI::getreferenceByseqNum($current_image_number + 1);
        $adl_echo['image_number'] = $current_image_number + 1;
        $adl_echo['image_src'] = $data_source['reference'];
    }

    echo json_encode($adl_echo);
}
