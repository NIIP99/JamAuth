<?php
namespace JamAuth;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\Utils;

use JamAuth\Command\JamAuthCommand;
use JamAuth\Lang\Translator;
use JamAuth\Task\Timing;
use JamAuth\Utils\Kitchen;

class JamAuth extends PluginBase{
    
    public $command = null;
    private $translator, $listener;
    
    public function onEnable(){
        define("JAMAUTH_VER", $this->getDescription()->getVersion());
        $this->translator = new Translator("en");
        $this->listener = new EventListener($this);
        $this->kitchen = new Kitchen($this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Timing($this), 6000);
        
        $this->loadConfig();
        $this->loadCommand();
        //update check
    }
    
    public function onDisable(){
        
    }
    
    private function loadConfig(){
        if(!is_dir($this->getDataFolder())){
            mkdir($this->getDataFolder());
        }
        $this->saveDefaultConfig();
        $this->saveResource("message.yml", false);
	$this->getKitchen()->putFridge((new Config($this->getDataFolder()."message.yml"))->getAll());
        return $this->getConfig()->getAll();
    }
    
    private function loadCommand(){
        $cm = $this->getServer()->getCommandMap();
        
        $cm->register("jamauth", new JamAuthCommand($this, "jamauth", $this->getTranslator()->translate("cmd.description")));
    }
    
    public function getTranslator(){
        return $this->translator;
    }
    
    public function getKitchen(){
        return $this->kitchen;
    }
    
    public function sendInfo($msg){
        echo "[JamAuth] ".$msg."\n";
    }
    
}
