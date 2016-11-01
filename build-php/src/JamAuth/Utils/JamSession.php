<?php
namespace JamAuth\Utils;

use pocketmine\Player;

use JamAuth\JamAuth;

class JamSession{
    
    const STATE_LOADING = 0;
    const STATE_PENDING = 1;
    const STATE_AUTHED = 2;
    
    private $plugin;
    private $p, $state, $attempts;
    private $hash = "";
    
    public function __construct(JamAuth $plugin, Player $p){
        $this->state = self::STATE_LOADING;
        $res = $plugin->getAPI()->execute("fetchUser", ["username" => $p->getName()]);
        $p->sendMessage($plugin->getKitchen()->getFood("join.message"));
        if(isset($res["hash"])){
            $p->sendMessage($plugin->getKitchen()->getFood("login.message"));
        }else{
            $p->sendMessage($plugin->getKitchen()->getFood("register.message"));
        }
        $this->p = $p;
    }
    
    public function getState(){
        return $this->state;
    }
    
}