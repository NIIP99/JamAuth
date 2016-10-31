<?php
namespace JamAuth\Utils;

use JamAuth\Utils\Recipe\JamAuthRecipe;
use JamAuth\Utils\Recipe\SimpleAuthRecipe;
use JamAuth\Utils\Recipe\ServerAuthRecipe;

class Kitchen{
    
    private $fridge = [];
    private $recipe;
    public static $TIME_FORMAT = "H:i:s";
    
    public function __construct($recipe){
        $type = $recipe["type"];
        switch($type){
            case "JamAuth":
                $this->recipe = new ServerAuthRecipe($recipe["data"]);
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
    }
    
    public function putFridge($foods){
        $this->fridge = $foods;
    }
    
    public function getFridge($name){
        $names = explode(".", $name);
        $food = $this->fridge;
        foreach($names as $n){
            if(isset($food[$n])){
                $food = $food[$n];
            }else{
                return $name;
            }
        }
        return $food;
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