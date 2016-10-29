<?php
namespace JamAuth;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\Utils;

use JamAuth\Command\JamAuthCommand;
use JamAuth\Lang\JamLang;
use JamAuth\Task\Timing;

class JamAuth extends PluginBase{
    
    public $command = null;
    private $lang, $listener;
    
    public function onEnable(){
        define("JAMAUTH_VER", $this->getDescription()->getVersion());
        $this->lang = new JamLang("en");
        $this->listener = new EventListener($this);
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
	$message = (new Config($this->getDataFolder()."message.yml"))->getAll();
        return $this->getConfig()->getAll();
    }
    
    private function loadCommand(){
        $cm = $this->getServer()->getCommandMap();
        
        $cm->register("jamauth", new JamAuthCommand($this, "jamauth", $this->getLang()->translate("main.commandDesc")));
    }
    
    public function getLang(){
        return $this->lang;
    }
    
    public function sendConsole($msg){
        echo "[JamAuth] ".$msg."\n";
    }
    
}
