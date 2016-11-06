<?php
namespace JamAuth\Importer;

use JamAuth\JamAuth;

abstract class DataImporter{
    
    protected $plugin;
    private $total = 0, $pace = 0;
    protected $reqID = "";
    
    public function __construct(JamAuth $plugin){
        $this->plugin = $plugin;
    }
    
    public function setTotal($total){
        $this->total = $total;
    }
    
    public function setPace($pace){
        $this->pace = $pace;
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