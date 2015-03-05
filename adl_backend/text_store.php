<?php

session_start();

require_once('dbManage/afTBAPI.php');
require_once('dbManage/amDBAPI.php');
require_once('dbManage/clientVerifyAPI.php');
require_once('validateClient.php');

if (validateClient()) {
    $current_time = getUTCtimeFraction();
    $dataIndex = uniqid("wrd_");
    afTBAPI::insert($_SESSION['mainIndex'], $dataIndex, $_POST['user_id'], 2, $current_time);
    amDBAPI::insert($dataIndex, $_POST['word'], $_POST['word']);
}

