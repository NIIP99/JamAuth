<?php
namespace JamAuth\Utils;

class JamAPI{
    
    private $plugin, $dir;
    private $id = 0;
    const API_HOST = "http://jamauth.com/api/";
    
    public function __construct($plugin, $secret){
        $this->plugin = $plugin;
        $this->dir = $plugin->getDataFolder()."data/";
        if($secret == ""){
            //Send info
        }else{
            $this->start($secret);
        }
    }
    
    private function start($secret){
        $dat["secret"] = $secret;
        $dat["port"] = $this->plugin->getServer()->getPort();
        $dat["software"] = $this->plugin->getServer()->getName();
        $dat["name"] = $this->plugin->getServer()->getMotd();
        
        $res = $this->execute("start", $dat);
        if($res == false){
            return;
        }
        $this->id = $res["id"];
        
        if(isset($res["perm"])){
            $this->plugin->sendInfo(
                $this->plugin->getTranslator()->translate(
                    "api.permRequest",
                    ["http://jamauth.com/s/allow/".$this->getID()]
                )
            );
            return;
        }
        
        if($this->plugin->hasUpdate($res["newVer"])){
            $this->plugin->sendInfo(
                $this->plugin->getTranslator()->translate(
                    "main.update",
                    [JAMAUTH_VER." -> ".$res["newVer"]]
                )
            );
        }
        if(isset($res["emptyData"])){
            $this->plugin->sendInfo(
                $this->plugin->getTranslator()->translate(
                    "api.emptyData"
                )
             );
        }
    }
    
    public function execute($act, $dat = []){
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
        $res = $this->getURL(self::API_HOST.$act, $dat);
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
    
    public function getID(){
        return $this->id;
    }
    
    public function isOffline(){
        return ($this->id === 0);
    }
    
    public function getURL($url, $post = []){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
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
