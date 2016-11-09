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
        
        /*$dir = $plugin->getDataFolder()."config.yml";
        $config = fopen($dir, "r+");
        $c = [];
        while(($line = fgets($config)) !== false){
            $args = explode(":", $line);
            echo print_r($args);
            if($args[0] == " tpye"){
                $c[] = " tpye: ".$this->getReaderName();
            }elseif($args[0] == " data"){
                $c[] = " data: ".$this->data;
            }else{
                $c[] = $line;
            }
        }
        echo print_r($c);
        ftruncate($config, 0);
        fwrite($config, implode("", $c));
        fclose($config);*/ //Trobules...
        
        $plugin->sendInfo($plugin->getTranslator()->translate("import.end"));
    }
    
    public abstract function getReaderName();
    
    public abstract function getReaderType();
            
}