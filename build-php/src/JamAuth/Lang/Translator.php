<?php
namespace JamAuth\Lang;

use pocketmine\utils\Config;

class Translator{
    
    private $msg;
    
    public function __construct($plugin, $ISO){
        $l_file = __DIR__."/".strtolower($ISO).".yml";
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
                $msg = str_replace("%$i%", self($arg), $msg);
                $i++;
            }
            return $msg;
        }
    }
    
    public function getNativeLanguage(){
        return $this->msg->get("name");
    }
}