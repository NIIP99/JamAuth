<?php
namespace JamAuth\Task;

use pocketmine\scheduler\PluginTask;
use JamAuth\JamAuth;

class JamTask extends PluginTask{
    
    private $plugin;
    private $tick = 0;
    
    public function __construct(JamAuth $plugin){
    	parent::__construct($plugin);
        $this->plugin = $plugin;
    }
    
    public function onRun($tick){
        $this->tick = $tick;
    }
    
    public function getTick(){
       return $this->tick;
    }
}