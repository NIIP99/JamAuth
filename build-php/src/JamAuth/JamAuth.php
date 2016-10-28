<?php
namespace JamAuth;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\Utils;

use JamAuth\Task\Timing;
use JamAuth\Command\JamAuthCommand;

class JamAuth extends PluginBase{
    
    public $command = null;
    private $listener;
    
    public function onEnable(){
        define("JAMAUTH_VER", $this->getDescription()->getVersion());
        $this->listener = new EventListener($this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Timing($this), 6000);
        
        $this->loadConfig();
        $this->loadCommand();
    }
    
    public function onDisable(){
        
    }
    
    private function loadConfig(){
        if(!is_dir($this->getDataFolder())){
            mkdir($this->getDataFolder());
        }
        $this->saveDefaultConfig();
        $this->saveResource("message.yml", false);
	$message = (new Config($this->getDataFolder()."message.yml"))->getAll();
        return $this->getConfig()->getAll();
    }
    
    private function loadCommand(){
        $cm = $this->getServer()->getCommandMap();
        
        $cm->register("jamauth", new JamAuthCommand($this, "jamauth", "JamAuth Core Command"));
    }
    
    private function loadSetting(){
        
    }
    
}
