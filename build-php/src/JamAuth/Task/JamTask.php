<?php
namespace JamAuth\Task;

use pocketmine\scheduler\PluginTask;
use JamAuth\JamAuth;

class JamTask extends PluginTask{
    
    private $tick = 0,
            $loginPacks = [];
    
    public function __construct(JamAuth $plugin){
    	parent::__construct($plugin);
        $this->plugin = $plugin;
    }
    
    public function onRun($tick){
        $this->tick = $tick;
        //TODO bulk login query
    }
    
    public function getTick(){
       return $this->tick;
    }
    
    public function pushLogin($username = null){
        if($username != null){
            $this->loginPacks[] = $username;
        }
    }
}