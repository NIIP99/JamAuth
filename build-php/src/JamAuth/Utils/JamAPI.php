<?php
namespace JamAuth\Utils;

class JamAPI{
    
    private $plugin, $dir, $err;
    private $id = 0, $CID = 0;
    const API_HOST = "http://jamauth.com/api/";
    
    public function __construct($plugin){
        $this->plugin = $plugin;
        
        $secret = $plugin->getDatabase()->getRule("secret");
        if($secret == null){
            $secret = "";
            $plugin->getDatabase()->setRule("secret", "");
        }
        
        $this->dir = $plugin->getDataFolder()."data/";
        if($secret == ""){
            $this->plugin->sendInfo(
                $this->plugin->getTranslator()->translate("api.nullSecret")
            );
        }else{
            $this->start($secret);
        }
    }
    
    private function start($secret){
        $dat["secret"] = $secret;
        $server = $this->plugin->getServer();
        $dat["port"] = $server->getPort();
        $dat["software"] = $server->getName()." ".$server->getVersion();
        $dat["name"] = $server->getMotd();
        
        $this->plugin->sendInfo($this->plugin->getTranslator()->translate("api.init"));
        $res = $this->execute("start", $dat);
        if($res == false){
            return;
        }
        
        if(isset($res["link"])){
            $this->plugin->sendInfo(
                $this->plugin->getTranslator()->translate(
                    "api.permRequest",
                    [$res["link"]]
                )
            );
            return;
        }
        
        $this->id = $res["id"];
        $this->CID = $res["CID"];
        
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
                $this->plugin->getTranslator()->translate("api.emptyData")
            );
        }
    }
    
    public function execute($act, $dat = []){
        $dat["CID"] = $this->CID;
        $res = $this->getURL(self::API_HOST.$act, $dat);
        if($this->hasError($res)){
            $this->err = $res;
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
    
    public function getError(){
        return $this->err;
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
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //TRUE
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->dir."cache"); 
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->dir."cache"); 
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
    
    private function hasError($res){
        if($res == false || $res == ""){
            return true;
        }
        return (strlen($res) == 2);
    }
}
