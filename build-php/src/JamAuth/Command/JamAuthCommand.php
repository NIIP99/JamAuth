<?php
namespace JamAuth\Command;

use JamAuth\JamAuth;
use JamAuth\Importer\SimpleAuthYAML;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

class JamAuthCommand extends Command implements PluginIdentifiableCommand{
    
    private $plugin;
    
    public function __construct(JamAuth $plugin, $name, $desc){
        $this->plugin = $plugin;
        parent::__construct($name, $desc, null, ["ja"]);
    }
    
    public function execute(CommandSender $sender, $alias, array $args){
        //Permission check
        
        switch($args[0]){
            case "import":
                $yaml = new SimpleAuthYAML($this->plugin);
                $yaml->read();
                break;
            default:
                
                break;
        }
    }
    
    public function getPlugin(){
        return $this->plugin;
    }
}
