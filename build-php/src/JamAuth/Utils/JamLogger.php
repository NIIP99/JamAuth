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
        $dir = $plugin->getDataFolder()."logs";
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        foreach(self::ENTRIES as $entry){
            /*if(!is_file($this->dir.$entry.".log")){
                
            }*/
            $this->resource[$entry] = fopen($dir."/".$entry.".log", "w");
            
        }
        
    }
    
    public function log($entry, $string){
        if(!in_array($entry, self::ENTRIES)){
            return false;
        }
        fwrite($this->resource[$entry], date(Kitchen::TIME_FORMAT)." $string\n");
    }
    
    public function end(){
        foreach($this->resource as $r){
            fclose($r);
        }
    }
    
}