<?php
namespace JamAuth\Utils;

class JamAPI{
    
    private $assetDir;
    const API_HOST = "http://jamauth.com/api/";
    
    public function __construct($secret, $assetDir){
        $this->assetDir = $assetDir;
        $this->start($secret);
        //Validate and process the API Session
    }
    
    private function start($secret){
        $result = $this->getURL(self::API_HOST."start?secret=".$secret."&ver=".JAMAUTH_VER);
        if(strlen($result) === 1){
            return $result;
        }
        return json_decode($result, true);
    }
    
    public function update($dat){
        $this->getURL(self::API_HOST."update?dat=".$dat);
    }
    
    public function fetchSettings(){
        return json_decode($this->getURL(self::API_HOST."fetch"), true);
    }
    
    public function check(){
        return json_decode($this->getURL(self::PROTOCOL."check"), true);
    }
    
    public function end(){
        $this->getURL(self::PROTOCOL."end");
    }
    
    public function getURL($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //TRUE
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->assetFolder."jamAuth.cache"); 
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->assetFolder."jamAuth.cache"); 
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
}
