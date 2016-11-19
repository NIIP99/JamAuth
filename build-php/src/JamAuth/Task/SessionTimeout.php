<?php
namespace JamAuth\Task;

use pocketmine\Player;

use pocketmine\scheduler\PluginTask;
use JamAuth\JamAuth;

class SessionTimeout extends PluginTask{
    
    private $p, $msg;
    
    public function __construct(JamAuth $plugin, Player $p){
    	parent::__construct($plugin);
        $this->p = $p;
        $this->msg = $plugin->getKitchen()->getFood("join.err.timeout");
    }
    
    public function onRun($tick){
        $this->p->kick($this->msg);
    }
}