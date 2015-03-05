<?php

require_once 'API.V1.abstract.php';
require_once 'dbManage/userCodeAPI.php';
require_once 'dbManage/aftTBAPI.php';
require_once 'dbManage/afTBAPI.php';
require_once 'dbManage/amDBAPI.php';
require_once 'validateClient.php';

class YADLAPI extends API
{
    protected $User;

    public function __construct($request) {
        parent::__construct($request);
    }
    
    protected function patient() {
        header("Content-Type: application/json");
        if(isset($this -> args["id"]) && userCodeAPI::exist_code($this -> args["id"])){
            if(isset($this -> args["span"])){
                $span = $this -> args["span"];
            } else {
                $span = 30;
            }
            $userid = userCodeAPI::getUserByCode($this -> args["id"]);
            $request_array = array();
            $request_array["id"] = $this -> args["id"];
            $request_array["time"] = getUTCtimeFraction()."+00:00";
            $request_array["span"] = $span;
            
            $adl_array = array();
            $mainIndexArray = aftTBAPI::getIndexBySpan($userid["userid"], $span);
            $request_array["count"] = sizeof($mainIndexArray);
            foreach($mainIndexArray as $mainIndex){
                $current_adl = array();
                $current_adl["time"] = getUTCtimeFraction()."+00:00";
                $current_adl["hard"] = afTBAPI::getActivityWordBymainIndex($mainIndex["mainIndex"], 2);
                $current_adl["medium"] = afTBAPI::getActivityWordBymainIndex($mainIndex["mainIndex"], 1);
                $current_adl["easy"] = afTBAPI::getActivityWordBymainIndex($mainIndex["mainIndex"], 0);
                array_push($adl_array, $current_adl);
            }
            
            $request_array["adl"] = array_values($adl_array);
            return $request_array;
        } else {
            $request_array = array("error" => "Authorization Error!");
            return $request_array;
        }
        
    }
    
    protected function metadata(){
        header("Content-Type: application/json");
        if(isset($this -> args["id"]) && userCodeAPI::exist_code($this -> args["id"])){
            $request_array["id"] = $this -> args["id"];
            $request_array["time"] = getUTCtimeFraction()."+00:00";
            $request_array["count"] = amDBAPI::getDatasetSize();
            $request_array["images"] = array_values(amDBAPI::getAllImagesIndexWord());
            
            return $request_array;
        } else {
            $request_array = array("error" => "Authorization Error!");
            return $request_array;
        }
    }
    
    protected function image(){
        header('Content-Type: image/jpeg');
       
        if(isset($this -> args["id"]) && isset($this -> args["img"]) && userCodeAPI::exist_code($this -> args["id"])){
            $reference = amDBAPI::getReferenceByIndex($this -> args["img"]);
            if(sizeof($reference) == 1){
                $filepath = "../".$reference["reference"];
                header('Content-Length: ' . filesize($filepath));
                readfile($filepath);
                return false;
            } else {
                $request_array = array("error" => "Image Not Exists!");
                return $request_array;
            }
        } else{
            $request_array = array("error" => "Authorization Error!");
            return $request_array;
        }
    }
}

