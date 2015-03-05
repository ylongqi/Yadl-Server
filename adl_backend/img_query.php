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

$img_feedback = 1;

if (validateClient()) {

    //$engine_instance = new engine();
    $adl_echo = array();
    $current_image_number = 0;
    $current_time = getUTCtimeFraction();

    if (isset($_POST['choice'])) {
        
        $database_size = aisTBAPI::getSizeByUserid($_POST["user_id"]);
        $current_dataIndex = aisTBAPI::getdataIndexByseqNum($_POST["img"]);
        afTBAPI::insert($_SESSION["mainIndex"], $current_dataIndex["dataIndex"], $_POST["user_id"], $_POST['choice'], $current_time);
        
        $current_image_number = intval($_POST["img"]);

        if ($current_image_number <= $database_size) {
            if ($_POST['choice'] == 2) {
                $adl_echo['previous_image_src'] = $_POST['src'];
            }
        }

        if ($current_image_number == $database_size) {
            $img_feedback = 0;
            $adl_echo["hard_list"] = afTBAPI::getCurrentHardActivity($_SESSION["mainIndex"]);
            aftTBAPI::insert($_SESSION["mainIndex"], $_POST["user_id"], $current_time);
            aisTBAPI::clear_user($_POST["user_id"]);
        }
    } else {
        if(!userCodeAPI::exist_user($_POST["user_id"])){
            $code = md5(uniqid("cod_"));
            userCodeAPI::insert($_POST["user_id"], $code);
        }
        
        aisTBAPI::clear_user($_POST["user_id"]);
        if(aftTBAPI::exist_user($_POST["user_id"])){
            $reference = afTBAPI::getActivityByUserid($_POST["user_id"]);
        } else {
            $reference = amDBAPI::getAllImages();
        }
        shuffle($reference);
        aisTBAPI::batchInsert($_POST["user_id"], $reference);
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
