<?php
namespace JamAuth\Utils;

use pocketmine\Player;

use JamAuth\JamAuth;
use JamAuth\Task\SessionTimeout;

class JamSession{
    
    const STATE_LOADING = 0;
    const STATE_PENDING = 1;
    const STATE_AUTHED = 2;
    
    private $plugin;
    private $p, $state, $attempts = 0, $step = 0, $saved;
    private $food = "", $salt = "";
    
    private $TaskID;
    
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
            $this->food = $res["food"];
            $this->salt = $res["salt"];
            $p->sendMessage($plugin->getKitchen()->getFood("login.message"));
        }else{
            $msg = ($plugin->conf["direct"]) ? $plugin->getKitchen()->getFood("register.step1") : $plugin->getKitchen()->getFood("register.message");
            $p->sendMessage($msg);
        }
        $this->p = $p;
        $this->plugin = $plugin;
        
        $time = $plugin->conf["authTimeout"];
        if($time > 0){
            $TimeoutTask = new SessionTimeout($plugin, $p);
            $plugin->getServer()->getScheduler()->scheduleDelayedTask($TimeoutTask, $time * 20);
            $this->TaskID = $TimeoutTask->getTaskId();
        }
        $this->state = self::STATE_PENDING;
    }
    
    public function getState(){
        return $this->state;
    }
    
    public function getPlayer(){
        return $this->p;
    }
    
    public function isRegistered(){
        return ($this->food != "");
    }
    
    public function direct($msg){
        switch($this->step){
            case 0:
                $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("register.step2"));
                $this->saved = $msg;
                $this->step++;
                break;
            case 1:
                if($this->saved === $msg){
                    unset($this->saved);
                    if(!$this->register($msg)){
                        $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("register.step1"));
                        --$this->step;
                    }
                    //TODO email configuration
                    //$this->step++;
                }else{
                    $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("register.err.notMatchPassword"));
                    $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("register.step1"));
                    --$this->step;
                }
                break;
            case 2:
                //Email
                break;
        }
    }
    
    public function register($pwd){
        $plugin = $this->plugin;
        $kitchen = $plugin->getKitchen();
        if($this->isRegistered()){
            $this->getPlayer()->sendMessage($kitchen->getFood("register.err.registered"));
            return false;
        }
        if($this->getState() == self::STATE_LOADING){
            $this->getPlayer()->sendMessage($kitchen->getFood("join.loading"));
            return false;
        }
        $minByte = $plugin->conf["minPasswordLength"];
        if($minByte > 0){
            if(strlen($pwd) < $minByte){
                $this->getPlayer()->sendMessage($kitchen->getFood("register.err.shortPassword"));
                return false;
            }
        }
        $salt = ($kitchen->getRecipe()->needSalt()) ? $kitchen->getSalt(16) : strtolower($this->getPlayer()->getName());
        $food = $kitchen->getRecipe()->cook($pwd, $salt);
        $time = time();
        
        if(!$plugin->getAPI()->isOffline()){
            //More work...
        }
        
        if(!$plugin->getDatabase()->register($this->getPlayer()->getName(), $time, $food, $salt)){
            $plugin->sendInfo($plugin->getTranslator()->translate("err.localRegister", [$this->getPlayer()->getName()])); //Error report
            $this->getPlayer()->sendMessage($kitchen->getFood("register.err.main"));
            return false;
        }
        
        $this->plugin->getLogger()->write(
            "register",
            $this->plugin->getTranslator()->translate(
                "logger.register",
                [$this->getPlayer()->getName(), $this->getPlayer()->getAddress()]
            )
        );
        $this->getPlayer()->sendMessage($plugin->getKitchen()->getFood("register.success"));
        $this->state = self::STATE_AUTHED;
        return true;
    }
    
    public function login($pwd){
        $kitchen = $this->plugin->getKitchen();
        if($this->getState() == self::STATE_LOADING){
            $this->getPlayer()->sendMessage($kitchen->getFood("join.loading"));
            return false;
        }
        if($this->attempts >= $this->plugin->conf["authAttempts"]){
            $this->getPlayer()->kick($this->plugin->getKitchen()->getFood("login.err.attempts"));
            return false;
        }
        $r = $kitchen->getRecipe();
        if(!$r->isSameFood($this->food, $r->cook($pwd, $this->salt))){
            $this->attempts++;
            $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("login.err.password"));
            return false;
        }
        
        $this->plugin->getLogger()->write(
            "login",
            $this->plugin->getTranslator()->translate(
                "logger.login",
                [$this->getPlayer()->getName(), $this->getPlayer()->getAddress()]
            )
        );
        $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("login.success"));
        $this->state = self::STATE_AUTHED;
        return true;
    }
    
    public function logout($byCmd = false){
        $this->plugin->getServer()->getScheduler()->cancelTask($this->TaskID);
        
        $state = ($this->getState() == self::STATE_AUTHED) ? "Authenticated" : "Guest";
        $this->plugin->getLogger()->write(
            "logout",
            $this->plugin->getTranslator()->translate(
                "logger.logout",
                [$this->getPlayer()->getName(), $this->getPlayer()->getAddress(), $state]
            )
        );
        $this->plugin->endSession($this->getPlayer()->getName());
        
        if($byCmd){
            $this->getPlayer()->sendMessage($this->plugin->getKitchen()->getFood("logout.message"));
            $this->plugin->startSession($this->getPlayer());
        }
    }
    
}