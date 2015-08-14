<?php
namespace OmhStack;
use \HttpRequest as HttpRequest;
use \DateTime as DateTime;

class OmhAuth{
    
    protected static $cliend_id = "yadl";
    protected static $client_secret = "IcpeG1Fbg2";
    #protected static $dsu_url = "https://lifestreams.smalldata.io/dsu/";
    protected static $dsu_url = "https://ohmage-omh.smalldata.io/dsu/";
    
    public static function redirect_address(){
        return OmhAuth::$dsu_url . 'oauth/authorize?client_id='
                .OmhAuth::$cliend_id.'&response_type=code';
    }
    
    //Code: Access Code returned from Omh Server
    public static function get_access_token($code){
        $encode_id_secret = base64_encode(OmhAuth::$cliend_id.':'.OmhAuth::$client_secret);
        $post_url = OmhAuth::$dsu_url . 'oauth/token';
        $post_data["code"] = $code;
        $post_data["grant_type"] = 'authorization_code';
        $post_header["Authorization"] = "Basic ".$encode_id_secret;
        
        $request = new HttpRequest($post_url, HttpRequest::METH_POST);
        $request -> addHeaders($post_header);
        $request -> addPostFields($post_data);
        $request -> send();
        
        $file = fopen("test.txt","w");
        fwrite($file,$request -> getResponseBody());
        fclose($file);
        
        return json_decode($request -> getResponseBody(), true);
    }
    
    //refresh_token: Refresh token returned from Omh Server
    public static function refresh_access_token($refresh_token){
        $encode_id_secret = base64_encode(OmhAuth::$cliend_id.':'.OmhAuth::$client_secret);
        $post_url = OmhAuth::$dsu_url . 'oauth/token';
        $post_data["refresh_token"] = $refresh_token;
        $post_data["grant_type"] = 'refresh_token';
        $post_header["Authorization"] = "Base ".$encode_id_secret;
        
        $request = new HttpRequest($post_url, HttpRequest::METH_POST);
        $request -> addHeaders($post_header);
        $request -> addPostFields($post_data);
        $request -> send();
        
        return json_decode($request -> getResponseBody(), true);
    }
    
    public static function check_valide_token($access_token){
        $get_url = OmhAuth::$dsu_url.'oauth/check_token?token='.$access_token;
        $request = new HttpRequest($get_url, HttpRequest::METH_GET);
        $request -> send();
        $respond = json_decode($request -> getResponseBody(), true);
        
        if(isset($respond["user_name"])){
            return $respond["user_name"];
        } else {
            return false;
        }
    
    }
    
    public static function send_datapoint($access_token, $body_data){
        $post_url = OmhAuth::$dsu_url.'dataPoints';
        $post_header["Authorization"] = "Bearer ".$access_token;
        $post_header["Content-type"] = "application/json";
        $post_data["header"] = OmhAuth::datapoint_header();
        $post_data["body"] = $body_data;
        
        $request = new HttpRequest($post_url, HttpRequest::METH_POST);
        $request -> addHeaders($post_header);
        $request -> setBody(json_encode($post_data));
        $request -> send();
        file_put_contents("test.txt", json_encode($post_data));
        
        if($request -> getResponseCode() == 201){
            return true;
        } else {
            return false;
        }
    }
    
    private static function datapoint_header(){
        $id = uniqid();
        $creation_date_time = date("Y-m-d\TH:i:sP");
        $schema_id["namespace"] = "yadl";
        $schema_id["name"] = "adl_web";
        $schema_id["version"] = "1.0";
        $acquisition_provenance["source_name"] = "yadl.yadagame.com";
        $acquisition_provenance["modality"] = "self-reported";
        
        $header["id"] = $id;
        $header["creation_date_time"] = $creation_date_time;
        $header["schema_id"] = $schema_id;
        $header["acquisition_provenance"] = $acquisition_provenance;
        
        return $header;
    }
    
}

