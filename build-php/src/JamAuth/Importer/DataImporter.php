<?php
namespace JamAuth\Importer;

use JamAuth\JamAuth;

abstract class DataImporter{
    
    private $plugin;
    private $total = 0, $pt = 0;
    protected $data = "[]";
    
    public function __construct(JamAuth $plugin){
        $this->plugin = $plugin;
    }
    
    protected function setTotal($total){
        $this->total = $total;
    }
    
    public function getTotal(){
        return $this->total;
    }
    
    protected function setPace($pace){
        $pt = round(($pace / $this->total) * 100);
        if($pt > $this->pt){
            $this->pt = $pt;
            $this->plugin->sendInfo("Importing: ".$pt."%");
        }
    }
    
    protected function write($data){
        if(!isset($data["hash"])){
            if($this->getReaderName() == "SimpleAuth"){
                $data["hash"] = $data["name"];
            }
        }
        $this->plugin->getDatabase()->register($data["name"], $data["time"], $data["food"], $data["hash"]);
    }
    
    protected function finalize(){
        $plugin = $this->plugin;
        
        foreach([
            "import.id" => "#",
            "import.last" => time()."-".$this->getReaderName()."-".$this->getReaderType(),
            "recipe.name" => $this->getReaderName(),
            "recipe.data" => $this->data
        ] as $name => $rule){
            $plugin->getDatabase()->setRule($name, $rule);
            
        }
        
        $plugin->sendInfo($plugin->getTranslator()->translate("import.end"));
    }
    
    public abstract function getReaderName();
    
    public abstract function getReaderType();
            
}