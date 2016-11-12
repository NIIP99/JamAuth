<?php
namespace JamAuth\Command;

use JamAuth\JamAuth;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use JamAuth\Utils\JamSession;

class LogoutCommand extends Command implements PluginIdentifiableCommand{
    
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
        $sess = $this->plugin->getSession($s->getName());
        if($sess != null && $sess->getState() == JamSession::STATE_AUTHED){
            $sess->logout(true);
        }
    }
    
    public function getPlugin(){
        return $this->plugin;
    }
}
