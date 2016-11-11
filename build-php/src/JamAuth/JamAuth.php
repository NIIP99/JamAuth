<?php
namespace JamAuth;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;

use JamAuth\Command\JamAuthCommand;
use JamAuth\Command\RegisterCommand;
use JamAuth\Command\LoginCommand;
use JamAuth\Command\LogoutCommand;
use JamAuth\Lang\Translator;
use JamAuth\Task\Timing;
use JamAuth\Utils\JamAPI;
use JamAuth\Utils\JamLogger;
use JamAuth\Utils\JamSession;
use JamAuth\Utils\Kitchen;
use JamAuth\Utils\LocalDatabase;

class JamAuth extends PluginBase{
    
    public $command = null;
    private $translator, $listener, $logger, $kitchen, $api, $db;
    public $conf = [];
    
    public function onEnable(){
        define("JAMAUTH_VER", $this->getDescription()->getVersion());
        
        $conf = $this->loadConfig();
        
        if(!isset($conf["secretKey"]) || strlen($conf["secretKey"]) != 36){
            $conf["secretKey"] = "";
        }
        $secret = $conf["secretKey"];
        unset($conf["secretKey"]);
        $dir = $this->getDataFolder()."data";
        if(!is_dir($dir)){
            mkdir($dir);
        }
        $this->conf = $conf;
        $this->logger = new JamLogger($this);
        $this->translator = new Translator($this);
        $this->db = new LocalDatabase($this);
        $this->kitchen = new Kitchen($this);
        if(!$this->loadCommand()){
            $this->sendInfo($this->getTranslator()->translate("err.cmd"));
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        $this->listener = new EventListener($this);
        $this->api = new JamAPI($this, $secret);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Timing($this), 6000);
        
        $this->sendInfo($this->getTranslator()->translate("main.loaded", [JAMAUTH_VER, "Offline"]));
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
        if($this->conf["command"]){
            $cm->register("login", new LoginCommand($this, "login", $this->getTranslator()->translate("cmd.login")));
            $cm->register("register", new RegisterCommand($this, "register", $this->getTranslator()->translate("cmd.register")));
            $cm->register("logout", new LogoutCommand($this, "logout", $this->getTranslator()->translate("cmd.logout")));
        }
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
    
    public function getDatabase(){
        return $this->db;
    }
    
    public function getAPI(){
        return $this->api;
    }
    
    public function startSession(Player $p){
        $this->session[strtolower($p->getName())] = new JamSession($this, $p);
    }
    
    public function getSession($pn){
        $spn = strtolower($pn);
        if(isset($this->session[$spn])){
            return $this->session[$spn];
        }else{
            return null;
        }
    }
    
    public function endSession($pn){
        unset($this->session[strtolower($pn)]);
    }
    
    public function hasUpdate($newVer){
        $thisVer = explode(".", JAMAUTH_VER);
        if($newVer["major"] > $thisVer[0]){
            return true;
        }elseif($newVer["major"] == $thisVer[0]){
            if($newVer["minor"] > $thisVer[1]){
                return true;
            }
        }
        return false;
    }
    
    public function sendInfo($msg){
        $msgs = explode("\n", $msg);
        foreach($msgs as $m){
            echo "- \e[1;48;5;197m[JamAuth]\e[0m ".$m."\n";
            $this->getLogger()->write("info", $m);
        }
    }
    
}
