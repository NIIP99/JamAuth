<?php
namespace JamAuth\Importer;

use JamAuth\JamAuth;

abstract class DataImporter{
    
    protected $plugin;
    private $total = 0, $pt = 0;
    
    public function __construct(JamAuth $plugin){
        $this->plugin = $plugin;
    }
    
    public function setTotal($total){
        $this->total = $total;
    }
    
    public function getTotal(){
        return $this->total;
    }
    
    public function setPace($pace){
        $pt = round(($pace / $this->total) * 100);
        if($pt > $this->pt){
            $this->pt = $pt;
            $this->plugin->sendInfo("Importing: ".$pt."%");
        }
    }
    
    public function write($data){
        if(!isset($data["hash"])){
            if($this->getReaderName() == "SimpleAuth"){
                $data["hash"] = $data["name"];
            }
        }
        $this->plugin->getDatabase()->register($data["name"], $data["time"], $data["food"], $data["hash"]);
    }
    
    public abstract function getReaderName();
    
    public abstract function getReaderType();
            
}