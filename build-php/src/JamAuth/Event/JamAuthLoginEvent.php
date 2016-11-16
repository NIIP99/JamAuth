<?php
namespace JamAuth\Event;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\event\Cancellable;

class JamAuthNullEvent extends PluginEvent implements Cancellable{
    
    public static $handlerList = null;
        
    public function __construct(){
        
    }
    
}
