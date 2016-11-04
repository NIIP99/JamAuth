<?php
namespace JamAuth\Command;

use JamAuth\JamAuth;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

class RegisterCommand extends Command implements PluginIdentifiableCommand{
    
    private $plugin;
    
    public function __construct(JamAuth $plugin, $name, $desc){
        $this->plugin = $plugin;
        parent::__construct($name, $desc);
    }
    
    public function execute(CommandSender $s, $alias, array $args){
        if(!$s instanceof Player){
            $s->sendMessage($this->plugin->getTranslator()->translate("cmd.sendAsPlayer"));
            return;
        }
        if(isset($args[0])){
            $this->plugin->getSession($s->getName())->register($args[0]);
        }else{
            $s->sendMessage($this->plugin->getKitchen()->getFood("register.err.noPassword"));
        }
    }
    
    public function getPlugin(){
        return $this->plugin;
    }
}
