<?php
namespace JamAuth\Task;

use pocketmine\scheduler\PluginTask;
use JamAuth\JamAuth;

class Timing extends PluginTask{
    
    private $plugin;
    
    public function __construct(JamAuth $plugin){
    	parent::__construct($plugin);
        $this->plugin = $plugin;
    }
    
    public function onRun($tick){
        
    }
}