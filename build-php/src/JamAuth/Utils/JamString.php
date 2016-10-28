<?php
namespace Plugswork\Utils;

use pocketmine\utils\Config;

class JamString{
    
    private static $TIME_FORMAT = "H:i:s";
    
    public function __construct(){
        
    }
    
    public static function loadUserMessage(){
        
    }
    
    public static function translate(){
        
    }
    
    public static function translateColor($string){
        return preg_replace_callback(
            "/(\\\&|\&)[0-9a-fk-or]/",
            function($matches){
                return str_replace("§r", "§r§f", str_replace("\\§", "&", str_replace("&", "§", $matches[0])));
            },
            $string
        );
    }
    
    public static function translateConstant($string, $name){
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
    }
}