<?php
namespace JamAuth;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use JamAuth\Command\JamAuthCommand;
use JamAuth\Command\RegisterCommand;
use JamAuth\Command\LoginCommand;
use JamAuth\Command\LogoutCommand;
use JamAuth\Lang\Translator;
use JamAuth\Task\Timing;
use JamAuth\Utils\JamAPI;
use JamAuth\Utils\JamLogger;
use JamAuth\Utils\Kitchen;

class JamAuth extends PluginBase{
    
    public $command = null;
    private $translator, $listener, $logger, $kitchen, $api;
    
    public function onEnable(){
        define("JAMAUTH_VER", $this->getDescription()->getVersion());
        
        $this->kitchen = new Kitchen($this);
        $conf = $this->loadConfig();
        $this->translator = new Translator($this, $conf["lang"]);
        $this->logger = new JamLogger($this, $conf["logging"]);
        if(!$this->loadCommand()){
            $this->sendInfo($this->getTranslator()->translate("err.cmd"));
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        $this->listener = new EventListener($this);
        $this->api = new JamAPI($this, $conf["secretKey"]);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Timing($this), 6000);
        
    }
    
    public function onDisable(){
        if(isset($this->logger)){
            $this->getLogger()->end();
        }
        if(isset($this->api)){
            $this->getAPI()->end();
        }
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
        
        foreach(["login", "register", "logout"] as $cmd){
            if($cm->getCommand($cmd) != null){
                return false;
            }
        }
        $cm->register("jamauth", new JamAuthCommand($this, "jamauth", $this->getTranslator()->translate("cmd.main")));
        $cm->register("login", new LoginCommand($this, "login", $this->getTranslator()->translate("cmd.login")));
        $cm->register("register", new RegisterCommand($this, "register", $this->getTranslator()->translate("cmd.register")));
        $cm->register("logout", new LogoutCommand($this, "logout", $this->getTranslator()->translate("cmd.logout")));
        return true;
    }
    
    public function getTranslator(){
        return $this->translator;
    }
    
    public function getKitchen(){
        return $this->kitchen;
    }
    
    public function getLogger(){
        return $this->logger;
    }
    
    public function getAPI(){
        return $this->api;
    }
    
    public function sendInfo($msg){
        echo "- \e[1;48;5;197m[JamAuth]\e[0m ".$msg."\n";
        $this->getLogger()->write("info", $msg);
    }
    
}
