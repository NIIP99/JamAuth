<?php
namespace JamAuth\Command;

use JamAuth\JamAuth;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

class LoginCommand extends Command implements PluginIdentifiableCommand{
    
    private $plugin;
    
    public function __construct(JamAuth $plugin, $name, $desc){
        $this->plugin = $plugin;
        parent::__construct($name, $desc, null, ["ja"]);
    }
    
    public function execute(CommandSender $sender, $alias, array $args){
        
    }
    
    public function getPlugin(){
        return $this->plugin;
    }
}
