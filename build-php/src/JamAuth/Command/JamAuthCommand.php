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

class JamAuthCommand extends Command implements PluginIdentifiableCommand{
    
    private $plugin;
    private $DataImporter;
    
    public function __construct(JamAuth $plugin, $name, $desc){
        $this->plugin = $plugin;
        parent::__construct($name, $desc, null, ["ja"]);
    }
    
    public function execute(CommandSender $sender, $alias, array $args){
        //Permission check
        
        switch($args[0]){
            case "import":
                $recipe = (isset($args[1])) ? strtolower($args[1]) : 0;
                if($recipe === "simpleauth"){
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
                }elseif($recipe === "serverauth"){
                    
                }else{
                    
                }
                if(isset($this->DataImporter)){
                    $this->DataImporter->read();
                    unset($this->DataImporter);
                }
                break;
            case "check":
                if(isset($this->DataImporter)){
                    $this->plugin->sendInfo($this->DataImporter->getPace() / $this->DataImporter->getTotal());
                }else{
                    $this->plugin->sendInfo(0);
                }
                break;
            default:
                
                break;
        }
    }
    
    public function getPlugin(){
        return $this->plugin;
    }
}
