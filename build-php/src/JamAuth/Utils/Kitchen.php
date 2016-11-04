<?php
namespace JamAuth\Utils;

use pocketmine\utils\Config;

use JamAuth\Utils\Recipe\JamAuthRecipe;
use JamAuth\Utils\Recipe\SimpleAuthRecipe;
use JamAuth\Utils\Recipe\ServerAuthRecipe;

class Kitchen{
    
    private $foods = [];
    private $recipe;
    public static $TIME_FORMAT = "Y-M-d H:i:s T";
    
    public function __construct($plugin){
        $recipe = $plugin->conf["recipe"];
        $type = $recipe["type"];
        switch($type){
            case "JamAuth":
                $this->recipe = new JamAuthRecipe($recipe["data"]);
                break;
            case "SimpleAuth":
                $this->recipe = new SimpleAuthRecipe($recipe["data"]);
                break;
            case "ServerAuth":
                $this->recipe = new ServerAuthRecipe($recipe["data"]);
                break;
            default:
                $this->recipe = new JamAuthRecipe($recipe["data"]);
                break;
        }
        $this->foods = new Config($plugin->getDataFolder()."message.yml", Config::YAML);
    }
    
    public function getFood($name, $args = []){
        if(empty($msg = $this->foods->getNested($name))){
            return $name;
        }else{
            $i = 0;
            foreach($args as $arg){           
                $msg = str_replace("%$i%", self::getFood($arg), $msg);
                $i++;
            }
            return $msg;
        }
    }
    
    public function getRecipe(){
        return $this->recipe;
    }
    
    public function getSalt($gram){
        $salt = "";
        $cabinet = "abcdefghijklmnopqrstuvwxyz0123456789";
        for($amt = 0; $amt < $gram; $amt++){
            $salt .= $cabinet[rand(0,35)];
        }
        return $salt;
    }
    
    public static function seasoning($string){
        return preg_replace_callback(
            "/(\\\&|\&)[0-9a-fk-or]/",
            function($matches){
                return str_replace("§r", "§r§f", str_replace("\\§", "&", str_replace("&", "§", $matches[0])));
            },
            $string
        );
    }
    
    /*public static function constant($string, $name){
        return str_replace(
            array(
                "{PLAYER}",
                "{TIME}",
                "{TOTALPLAYERS}",
                "{MAXPLAYERS}"
            ),
            array(
                $name,
                date(self::$TIME_FORMAT),
                count(self::$plugin->getServer()->getOnlinePlayers()),
                self::$maxP
            ),
            $string
        );
    }*/
}