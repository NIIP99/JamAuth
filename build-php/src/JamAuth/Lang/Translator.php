<?php
namespace JamAuth\Lang;

use pocketmine\utils\Config;

class Translator{
    
    private $msg;
    
    public function __construct($plugin){
        $lang = $plugin->conf["lang"];
        $l_file = __DIR__."/".strtolower($lang).".yml";
        if(!is_file($l_file)){
            $plugin->sendInfo("Invalid Language");
            $l_file = __DIR__."/en.yml";
        }
        $this->msg = new Config($l_file, Config::YAML);
    }
    
    public function translate($key, $args = []){
        if(empty($msg = $this->msg->getNested($key))){
            return $key;
        }else{
            $i = 0;
            foreach($args as $arg){           
                $msg = str_replace("%$i%", self::translate($arg), $msg);
                $i++;
            }
            return $msg;
        }
    }
    
    public function getLanguageName(){
        return $this->msg->get("name");
    }
}