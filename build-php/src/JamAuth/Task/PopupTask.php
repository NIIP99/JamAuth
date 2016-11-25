<?php
namespace JamAuth\Task;

use pocketmine\scheduler\PluginTask;

use JamAuth\JamAuth;
use JamAuth\Utils\JamSession;

class PopupTask extends PluginTask{
    
    public function __construct(JamAuth $plugin){
    	parent::__construct($plugin);
    }
    
    public function onRun($tick){
        foreach($this->owner->getAllSessions() as $s){
            if($s->getState() < JamSession::STATE_AUTHED){
                $s->getPlayer()->sendPopup();
            }
        }
    }
}