<?php
namespace JamAuth\Utils;

use pocketmine\Player;

use JamAuth\JamAuth;

class JamSession{
    
    const STATE_LOADING = 0;
    const STATE_PENDING = 1;
    const STATE_AUTHED = 2;
    
    private $plugin;
    private $p, $state, $attempts = 0;
    private $hash = "", $salt = "";
    
    public function __construct(JamAuth $plugin, Player $p){
        $this->state = self::STATE_LOADING;
        $data = [
            "username" => $p->getName(),
            "ip" => $p->getIp()
        ];
        $res = $plugin->getAPI()->execute("fetchUser", $data);
        $p->sendMessage($plugin->getKitchen()->getFood("join.message"));
        if(isset($res["hash"])){
            $this->hash = $res["hash"];
            $this->salt = $res["salt"];
            $p->sendMessage($plugin->getKitchen()->getFood("login.message"));
        }else{
            $p->sendMessage($plugin->getKitchen()->getFood("register.message"));
        }
        $this->p = $p;
        $this->plugin = $plugin;
        
        $this->state = self::STATE_PENDING;
    }
    
    public function getState(){
        return $this->state;
    }
    
    public function getPlayer(){
        return $this->p;
    }
    
    public function register($pwd){
        $minByte = $this->plugin->conf["minPasswordLength"];
        $kitchen = $this->plugin->getKitchen();
        if($minByte > 0){
            if(strlen($pwd) < $minByte){
                $this->getPlayer()->sendMessage($kitchen->getFood("register.err.shortPassword"));
                return false;
            }
        }
        $salt = $kitchen->getSalt(16); //Maybe allow salt bytes customization?
        $food = $kitchen->getRecipe()->cook($pwd, $salt);
        
        //More work...
        
        $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("register.success"));
        $this->state = self::STATE_AUTHED;
        return true;
    }
    
    public function login($pwd){
        if($this->attempts >= $this->plugin->conf["authAttempts"]){
            $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("login.err.attempts"));
            return false;
        }
        $r = $this->plugin->getKitchen()->getRecipe();
        if(!$r->isCookedWith($this->hash, $r->cook($pwd, $this->salt), $this->salt)){
            $this->attempts++;
            $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("login.err.password"));
            return false;
        }
        $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("login.success"));
        $this->state = self::STATE_AUTHED;
        return true;
    }
    
}