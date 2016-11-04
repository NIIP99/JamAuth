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
            "username" => $p->getName()
        ];
        if($plugin->getAPI()->isOffline()){
            $res = $plugin->getDatabase()->fetchUser($p->getName());
        }else{
            $res = $plugin->getAPI()->execute("fetchUser", $data);
        }
        $p->sendMessage($plugin->getKitchen()->getFood("join.message"));
        if(isset($res["food"])){
            $this->hash = $res["food"];
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
        $kitchen = $this->plugin->getKitchen();
        if($this->getState == self::STATE_LOADING){
            $this->getPlayer()->sendMessage($kitchen->getFood("join.loading"));
            return false;
        }
        $minByte = $this->plugin->conf["minPasswordLength"];
        if($minByte > 0){
            if(strlen($pwd) < $minByte){
                $this->getPlayer()->sendMessage($kitchen->getFood("register.err.shortPassword"));
                return false;
            }
        }
        $salt = $kitchen->getSalt(16); //Maybe allow salt bytes customization?
        $food = $kitchen->getRecipe()->cook($pwd, $salt);
        $time = time();
        
        if(!$this->plugin->getAPI()->isOffline()){
            //More work...
        }
        
        if(!$this->plugin->getDatabase()->register($this->getPlayer()->getName(), $time, $food, $salt)){
            $this->plugin->sendInfo(); //Error report
        }
        $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("register.success"));
        $this->state = self::STATE_AUTHED;
        return true;
    }
    
    public function login($pwd){
        $kitchen = $this->plugin->getKitchen();
        if($this->getState == self::STATE_LOADING){
            $this->getPlayer()->sendMessage($kitchen->getFood("join.loading"));
            return false;
        }
        if($this->attempts >= $this->plugin->conf["authAttempts"]){
            $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("login.err.attempts"));
            return false;
        }
        $r = $kitchen->getRecipe();
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