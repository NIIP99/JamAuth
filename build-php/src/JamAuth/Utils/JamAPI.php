<?php
namespace JamAuth\Utils;

class JamAPI{
    
    private $plugin, $dir;
    const API_HOST = "http://jamauth.com/api/";
    
    public function __construct($plugin, $secret){
        $this->plugin = $plugin;
        $this->dir = $this->plugin->getDataFolder();
        if($this->start($secret) !=  false){
            //Update and validation
        }
    }
    
    private function start($secret){
        $dat = [];
        //Load data
        $res = $this->getURL(self::API_HOST."start?sec=".$secret."&dat=".json_encode($dat));
        if($this->hasError($res)){
            if($res == false){
                $res = 00;
            }
            $this->plugin->sendInfo(
                    $this->plugin->getTranslator()->translate(
                            "api.startError",
                            ["err.".$res]
                    )
            );
            return false;
        }
        return json_decode($res, true);
    }
    
    public function execute($act, $dat){
        //Data Validator
        if(!is_array($dat)){
            $this->plugin->sendInfo(
                    $this->plugin->getTranslator()->translate(
                            "api.execError",
                            ["err.null"]
                    )
            );
            return false;
        }
        $json = json_encode($dat);
        $res = $this->getURL(self::API_HOST."exec?act=".$act."dat=".$json);
        if($this->hasError($res)){
            $this->plugin->sendInfo(
                    $this->plugin->getTranslator()->translate(
                            "api.execError",
                            ["err.".$res]
                    )
            );
            return false;
        }
        return json_decode($res, true);
    }
    
    public function check(){
        return json_decode($this->getURL(self::API_HOST."check"), true);
    }
    
    public function end(){
        $this->getURL(self::API_HOST."end");
    }
    
    public function getURL($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //TRUE
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->dir."cache"); 
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->dir."cache"); 
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
    
    private function hasError($res){
        if($res == false){
            return true;
        }
        return (strlen($res) == 2);
    }
}
