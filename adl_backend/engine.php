<?php

//require_once('dbManage/picInfoAPI.php');

class engine {

    public function flavor_statistic($gameInfo) {

        $positive_instance = array();
        $negative_instance = array();

        $rowNum = $gameInfo->num_rows;

        for ($i = 0; $i < $rowNum; $i ++) {
            $r = $gameInfo->fetch_assoc();

            if ($r["choice"] == 1) {
                array_push($positive_instance, $r["firstPic"]);
                array_push($negative_instance, $r["secondPic"]);
            } else if ($r["choice"] == 2) {
                array_push($positive_instance, $r["secondPic"]);
                array_push($negative_instance, $r["firstPic"]);
            } else if ($r["choice"] == 3) {
                array_push($negative_instance, $r["firstPic"]);
                array_push($negative_instance, $r["secondPic"]);
            }
        }

        $statistic = array();
        if (sizeof($positive_instance) <= 0){
            $statistic["reset"] = "true";
        }

        $statistic["positive"] = $this -> getStatisticVector($positive_instance);
        $statistic["negative"] = $this -> getStatisticVector($negative_instance);
        $remarkableString = $this -> remarkableElement($statistic["positive"], $statistic["negative"]);
        $statistic["posiRemark"] = $this->remarkableFoodImage($remarkableString, $positive_instance);
        $statistic["negaRemark"] = $this->remarkableFoodImage($remarkableString, $negative_instance);
        $statistic["remark"] = $remarkableString;
        
        return $statistic;
    }
    
    private function remarkableFoodImage($remarkableString, $idList){
        
        $foodImageList = array();
        
        for($i = 0; $i < sizeof($idList); $i ++){
            $imageInfo = array();
            $recipe = recipeSourceAPI::getFeatureVectorByID($idList[$i]);
            $imageInfo["imgSrc"] = $recipe["imgSrc"];
            $imageInfo["remarkValue"] = $recipe[$remarkableString];
            array_push($foodImageList, $imageInfo);
        }
        
        return array_values($foodImageList);
    }
    
    private function remarkableElement($positive_s, $negative_s){
        
        $remarkableString = "";
        $remarkableValue = 0;
        
        $keyArray = array("FASAT", "GLUS", "FAMS", "FAT", "PROCNT", "STARCH",
            "CA", "NA", "ZN", "VITC", "MN", "K",
            "Piquant", "Bitter", "Sweet", "Meaty", "Salty", "Sour");
        
        for($i = 0; $i < sizeof($keyArray); $i ++){
            if($positive_s[$keyArray[$i]] + $negative_s[$keyArray[$i]] > 0){
                $presentValue = abs($positive_s[$keyArray[$i]] - $negative_s[$keyArray[$i]]) / 
                    max($positive_s[$keyArray[$i]], $negative_s[$keyArray[$i]]);
                
                if($presentValue > $remarkableValue && $positive_s[$keyArray[$i]] > 0 && $negative_s[$keyArray[$i]] > 0){
                    $remarkableString = $keyArray[$i];
                    $remarkableValue = $presentValue;
                }
            }
        }
        
        return $remarkableString;
    }
    
    private function getStatisticVector($idList){
        
        $feature_vector["FASAT"] = array();
        $feature_vector["GLUS"] = array();
        $feature_vector["FAMS"] = array();
        $feature_vector["FAT"] = array();
        $feature_vector["PROCNT"] = array();
        $feature_vector["STARCH"] = array();
        
        $feature_vector["CA"] = array();
        $feature_vector["NA"] = array();
        $feature_vector["ZN"] = array();
        $feature_vector["VITC"] = array();
        $feature_vector["MN"] = array();
        $feature_vector["K"] = array();
        
        $feature_vector["Piquant"] = array();
        $feature_vector["Bitter"] = array();
        $feature_vector["Sweet"] = array();
        $feature_vector["Meaty"] = array();
        $feature_vector["Salty"] = array();
        $feature_vector["Sour"] = array();
        
        for($i = 0; $i < sizeof($idList); $i ++){
            $recipe = recipeSourceAPI::getFeatureVectorByID($idList[$i]);
            
            if($this -> checkComplete($recipe)){
                array_push($feature_vector["FASAT"], $recipe["FASAT"]);
                array_push($feature_vector["GLUS"], $recipe["GLUS"]);
                array_push($feature_vector["FAMS"], $recipe["FAMS"]);
                array_push($feature_vector["FAT"], $recipe["FAT"]);
                array_push($feature_vector["PROCNT"], $recipe["PROCNT"]);
                array_push($feature_vector["STARCH"], $recipe["STARCH"]);
                
                array_push($feature_vector["CA"], $recipe["CA"]);
                array_push($feature_vector["NA"], $recipe["NA"]);
                array_push($feature_vector["ZN"], $recipe["ZN"]);
                array_push($feature_vector["VITC"], $recipe["VITC"]);
                array_push($feature_vector["MN"], $recipe["MN"]);
                array_push($feature_vector["K"], $recipe["K"]);
            }
            array_push($feature_vector["Piquant"], $recipe["Piquant"]);
            array_push($feature_vector["Bitter"], $recipe["Bitter"]);
            array_push($feature_vector["Sweet"], $recipe["Sweet"]);
            array_push($feature_vector["Salty"], $recipe["Salty"]);
            array_push($feature_vector["Meaty"], $recipe["Meaty"]);
            array_push($feature_vector["Sour"], $recipe["Sour"]);
        }
        
        $key_array = array_keys($feature_vector);
        $statistic = array();
        
        for($i = 0; $i < sizeof($key_array); $i ++){
            $statistic[$key_array[$i]] = array_sum($feature_vector[$key_array[$i]]) / count($feature_vector[$key_array[$i]]);
            $statistic[$key_array[$i] . "_E"] = stats_standard_deviation($feature_vector[$key_array[$i]]) / pow(count($feature_vector[$key_array[$i]]), 0.5);
        }
        
        return $statistic;
    }
    
    private function checkComplete($feature_vector){
        
        $total = $feature_vector["FASAT"] + $feature_vector["GLUS"] + $feature_vector["FAMS"] +
                $feature_vector["FAT"] + $feature_vector["PROCNT"] + $feature_vector["STARCH"] +
                $feature_vector["CA"] + $feature_vector["NA"] + $feature_vector["ZN"] +
                $feature_vector["VITC"] + $feature_vector["MN"] + $feature_vector["K"];
        
        if($total > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function image_feedback($gameInfo, $email) {

        $all_instance = array();

        if (!is_numeric($gameInfo)) {
            $rowNum = $gameInfo->num_rows;

            for ($i = 0; $i < $rowNum; $i ++) {
                $r = $gameInfo->fetch_assoc();
                array_push($all_instance, $r["firstPic"]);
                array_push($all_instance, $r["secondPic"]);
            }
        }

        $r_List = foodPicAPI::getRecipeArrayByEmail($email);
        shuffle($r_List);
        //file_put_contents('a.txt', json_encode($r_List_sh));
        
        $food_array = array();
        for($i = 0; $i < sizeof($r_List); $i ++){
            if(!in_array($r_List[$i], $all_instance)){
                array_push($food_array, $r_List[$i]);
            }
            if(sizeof($food_array) >= 2){
                break;
            }
        }
        
        $food_pair = $this -> get_food_pair($food_array[0], $food_array[1]);

        return $food_pair;
    }

    private function get_food_pair($food_1, $food_2) {

        $food_pair = array();
        $food_1_row = recipeSourceAPI::getFeatureVectorByID($food_1);
        $food_2_row = recipeSourceAPI::getFeatureVectorByID($food_2);

        $food_pair["first"] = $food_1;
        $food_pair["src_1"] = $food_1_row["imgSrc"];
        $food_pair["description_1"] = urldecode($food_1_row["name"]);

        $food_pair["second"] = $food_2;
        $food_pair["src_2"] = $food_2_row["imgSrc"];
        $food_pair["description_2"] = urldecode($food_2_row["name"]);

        return $food_pair;
    }

}