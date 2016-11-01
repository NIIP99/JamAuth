<?php
namespace JamAuth\Utils;

use JamAuth\Utils\Kitchen;
use JamAuth\JamAuth;

class JamLogger{
    
    private $resource = [];
    const ENTRIES = [
        "login",
        "register",
        "info"
    ];
    
    public function __construct(JamAuth $plugin){
        $enabled = $plugin->conf["logging"];
        $dir = $plugin->getDataFolder()."logs";
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        foreach(self::ENTRIES as $entry){
            if($enabled[$entry] != true){
                $res = false;
            }else{
                $res = fopen($dir."/".$entry.".log", "w");
            }
            $this->resource[$entry] = $res;
        }
    }
    
    public function write($entry, $string){
        if(!in_array($entry, self::ENTRIES) || $this->resource[$entry] == false){
            return false;
        }
        fwrite($this->resource[$entry], "[".date(Kitchen::$TIME_FORMAT)."] $string\n");
    }
    
    public function end(){
        foreach($this->resource as $r){
            if($r != false){
                fclose($r);
            }
        }
    }
    
}