<?php
namespace JamAuth\Task;

use pocketmine\Player;

use pocketmine\scheduler\Task;
use JamAuth\JamAuth;

class Timing extends Task{
    
    private $p;
    
    public function __construct(Player $p){
        $this->p = $p;
    }
    
    public function onRun($tick){
        $p->kick();
    }
}