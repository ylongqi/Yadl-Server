<?php

abstract class API
{
        
    protected $method = '';
    protected $endpoint = '';
    protected $args = Array();

    /**
     * Constructor: __construct
     */
    public function __construct($request) {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: GET");
        //header("Content-Type: application/json");

        $this->endpoint = $request;
        $this->method = $_SERVER['REQUEST_METHOD'];

        switch($this->method) {
        case 'GET':
            $this -> args = $this->_cleanInputs($_GET);
            break;
        default:
            $this-> _response('Invalid Method', 405);
            break;
        }
    }
    
    public function processAPI() {
        if ((int)method_exists($this, $this -> endpoint) > 0) {
            return $this -> _response($this->{$this->endpoint}($this->args));
        }
        return $this->_response("No Endpoint: $this->endpoint", 404);
    }

    private function _response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        
        if($data){
            return json_encode($data);
        } else {
            return false;
        }
    }

    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ); 
        return ($status[$code])?$status[$code]:$status[500]; 
    }
}

