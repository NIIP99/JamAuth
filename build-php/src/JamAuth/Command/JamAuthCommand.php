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
use JamAuth\Importer\ServerAuthMySQL;
use JamAuth\Importer\ServerAuthYAML;
use JamAuth\Utils\Kitchen;

class JamAuthCommand extends Command implements PluginIdentifiableCommand{
    
    private $plugin;
    private $DataImporter;
    
    public function __construct(JamAuth $plugin, $name, $desc){
        $this->plugin = $plugin;
        parent::__construct($name, $desc, null, ["jam"]);
    }
    
    public function execute(CommandSender $s, $alias, array $args){
        //Permission check
        if($s instanceof Player){
            $s->sendMessage($this->plugin->getTranslator()->translate("cmd.sendAsConsole"));
            return;
        }
        if(!isset($args[0])){
            $args[0] = "";
        }
        switch($args[0]){
            case "secret":
                if(isset($args[1])){
                    $this->plugin->getDatabase()->setRule("secret", $args[1]);
                    $this->plugin->sendInfo(
                        $this->plugin->getTranslator()->translate(
                            "main.secretUpdated"
                        )
                    );
                }else{
                    $this->plugin->sendInfo("Use: /jam secret <key>");
                }
                break;
            case "import":
                $arg = (isset($args[1])) ? strtolower($args[1]) : 0;
                $type = (isset($args[2])) ? strtolower($args[2]) : 0;
                if($arg === "simpleauth"){
                    if($type === "mysql"){
                        $this->DataImporter = new SimpleAuthMySQL($this->plugin);
                    }elseif($type === "yaml"){
                        $this->DataImporter = new SimpleAuthYAML($this->plugin);
                    }elseif($type === "sqlite"){
                        $this->DataImporter = new SimpleAuthSQLite($this->plugin);
                    }else{
                        $this->plugin->sendInfo("Use: /jam import simpleauth <mysql/yaml/sqlite>");
                    }
                }elseif($arg === "serverauth"){
                    if($type === "mysql"){
                        $this->DataImporter = new ServerAuthMySQL($this->plugin);
                    }elseif($type === "yaml"){
                        $this->DataImporter = new ServerAuthYAML($this->plugin);
                    }else{
                        $this->plugin->sendInfo("Use: /jam import serverauth <mysql/yaml>");
                    }
                }elseif($arg === "confirm"){
                    if(isset($this->DataImporter)){
                        $this->plugin->getDatabase()->truncate();
                        $DI = $this->DataImporter;
                        $this->plugin->sendInfo($this->plugin->getTranslator()->translate(
                            "import.start",
                            [$DI->getTotal(), $DI->getReaderName()." (".$DI->getReaderType().")"]
                        ));
                        unset($DI);
                        $this->DataImporter->import();
                        unset($this->DataImporter);
                    }
                }else{
                    $this->plugin->sendInfo("Use: /jam import <simpleauth/serverauth>");
                }
                if(isset($this->DataImporter)){
                    $this->plugin->sendInfo($this->plugin->getTranslator()->translate("main.importPrepare", [$this->DataImporter->getReaderName()]));
                    $res = $this->DataImporter->prepare();
                    if($res === true){
                        $this->plugin->sendInfo($this->plugin->getTranslator()->translate("main.confirmImport"));
                    }else{
                        $this->plugin->sendInfo($this->plugin->getTranslator()->translate("import.prepareError", [$res]));
                        unset($this->DataImporter);
                    }
                }
                break;
            case "check":
                $mode = ($this->plugin->getAPI()->isOffline()) ? "Offline (".$this->plugin->getAPI()->getError().")" : "Online  (".$this->plugin->getAPI()->getID().")";
                $this->plugin->sendInfo(
                        "Version: ".JAMAUTH_VER."\n".
                        "Mode: ".$mode."\n".
                        "Database: ".date(Kitchen::$TIME_FORMAT, $this->plugin->getDatabase()->getRule("record"))."\n".
                        "Recipe: ".$this->plugin->getKitchen()->getRecipe()->getName()."\n"
                );
                break;
            case "reload":
                $this->plugin->reload();
                break;
            case "set":
                //Rule settings, maybe
                break;
            default:
                $this->plugin->sendInfo("Use: /jam <secret/import/check/reload>");
                break;
        }
    }
    
    public function getPlugin(){
        return $this->plugin;
    }
}
