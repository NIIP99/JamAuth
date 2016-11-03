<?php
namespace JamAuth\Utils;

class JamAPI{
    
    private $plugin, $dir;
    private $offline = true;
    const API_HOST = "http://jamauth.com/api/";
    
    public function __construct($plugin, $secret){
        $this->dir = $plugin->getDataFolder()."data/";
        if($secret == ""){
            //Send info
        }else{
            if(($res = $this->start($secret)) !=  false){
                if($plugin->hasUpdate($res["newVer"])){
                    $plugin->sendInfo(
                        $plugin->getTranslator()->translate(
                            "main.update",
                            [JAMAUTH_VER." -> ".$res["newVer"]]
                        )
                    );
                }
                if(isset($res["emptyData"])){
                    $plugin->sendInfo(
                        $plugin->getTranslator()->translate(
                                "api.emptyData"
                        )
                    );
                }
                $this->offline = false;
            }
        }
        $this->plugin = $plugin;
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
        $res = $this->getURL(self::API_HOST.$act."?dat=".$json);
        if($this->hasError($res)){
            $this->plugin->sendInfo(
                    $this->plugin->getTranslator()->translate(
                            "api.execError",
                            ["err.".$res]
                    )
            );
            return false;
        }
        json_decode($res, true);
    }
    
    public function check(){
        return json_decode($this->getURL(self::API_HOST."check"), true);
    }
    
    public function end(){
        $this->getURL(self::API_HOST."end");
    }
    
    public function isOffline(){
        return $this->offline;
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
        if($res == false || $res = ""){
            return true;
        }
        return (strlen($res) == 2);
    }
}
