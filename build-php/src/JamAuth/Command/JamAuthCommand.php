<?php
namespace JamAuth\Command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use JamAuth\JamAuth;
use JamAuth\Importer\SimpleAuthMySQL;
use JamAuth\Importer\SimpleAuthSQLite;
use JamAuth\Importer\SimpleAuthYAML;
use JamAuth\Task\ImportTask;

class JamAuthCommand extends Command implements PluginIdentifiableCommand{
    
    private $plugin;
    private $DataImporter;
    
    public function __construct(JamAuth $plugin, $name, $desc){
        $this->plugin = $plugin;
        parent::__construct($name, $desc, null, ["ja"]);
    }
    
    public function execute(CommandSender $s, $alias, array $args){
        //Permission check
        if($s instanceof Player){
            $s->sendMessage($this->plugin->getTranslator()->translate("cmd.sendAsConsole"));
            return;
        }
        
        switch($args[0]){
            case "import":
                $arg = (isset($args[1])) ? strtolower($args[1]) : 0;
                if($arg === "simpleauth"){
                    $type = (isset($args[2])) ? strtolower($args[2]) : 0;
                    if($type === "mysql"){
                        $this->DataImporter = new SimpleAuthMySQL($this->plugin);
                    }elseif($type === "yaml"){
                        $this->DataImporter = new SimpleAuthYAML($this->plugin);
                    }elseif($type === "sqlite"){
                        $this->DataImporter = new SimpleAuthSQLite($this->plugin);
                    }else{
                        //Implement Auto Selector
                    }
                }elseif($arg === "serverauth"){
                    
                }elseif($arg === "confirm"){
                    if(isset($this->DataImporter)){
                        $this->plugin->sendInfo($this->plugin->getTranslator()->translate("main.importPrepare", [$this->DataImporter->getReaderName()]));
                        $this->DataImporter->import();
                        unset($this->DataImporter);
                    }
                }else{
                    
                }
                if(isset($this->DataImporter)){
                    $this->plugin->sendInfo($this->plugin->getTranslator()->translate("main.confirmImport"));
                }
                break;
            case "check":
                $this->plugin->sendInfo("Version: ".JAMAUTH_VER);
                break;
            default:
                $this->plugin->sendInfo();
                break;
        }
    }
    
    public function getPlugin(){
        return $this->plugin;
    }
}
