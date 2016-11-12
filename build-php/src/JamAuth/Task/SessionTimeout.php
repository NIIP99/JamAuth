<?php
namespace JamAuth\Task;

use pocketmine\Player;

use pocketmine\scheduler\PluginTask;
use JamAuth\JamAuth;

class SessionTimeout extends PluginTask{
    
    private $p;
    
    public function __construct(JamAuth $plugin, Player $p){
    	parent::__construct($plugin);
        $this->p = $p;
    }
    
    public function onRun($tick){
        $p->kick();
    }
}