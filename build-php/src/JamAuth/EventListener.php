<?php
namespace JamAuth;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\plugin\MethodEventExecutor;
use pocketmine\event\EventPriority;

use JamAuth\JamAuth;
use JamAuth\Utils\JamSession;

class EventListener implements Listener{
    
    private $plugin;
    
    public function __construct(JamAuth $plugin){
        $this->plugin = $plugin;
        $s = explode(":", $plugin->conf["suspend"]);
        $pm = Server::getInstance()->getPluginManager();
        
        $pm->registerEvent(PlayerPreLoginEvent::class, $this, EventPriority::NORMAL, new MethodEventExecutor("onPreLogin"), $plugin);
        $pm->registerEvent(PlayerJoinEvent::class, $this, EventPriority::NORMAL, new MethodEventExecutor("onJoin"), $plugin);
        
        if($plugin->conf["direct"]){
            $pm->registerEvent(PlayerCommandPreprocessEvent::class, $this, EventPriority::NORMAL, new MethodEventExecutor("onCommand"), $plugin);
        }
        $pm->registerEvent(PlayerQuitEvent::class, $this, EventPriority::NORMAL, new MethodEventExecutor("onQuit"), $plugin);
        
        foreach($s as $act){
            if(in_array($act, ["move", "chat", "dropitem", "break", "interact", "consume"])){
                switch($act){
                    case "move":
                        $event = PlayerMoveEvent::class;
                        break;
                    case "chat":
                        $event = PlayerChatEvent::class;
                        break;
                    case "dropitem":
                        $event = PlayerDropItemEvent::class;
                        break;
                    case "break":
                        $event = BlockBreakEvent::class;
                        break;
                    case "interact":
                        $event = PlayerInteractEvent::class;
                        break;
                    case "consume":
                        $event = PlayerItemConsumeEvent::class;
                        break;
                }
                $pm->registerEvent($event, $this, EventPriority::NORMAL, new MethodEventExecutor("on_".$act), $this->plugin);
            }else{
                $this->plugin->sendInfo($this->plugin->getTranslator()->translate("err.invalidEvent", [$act]));
            }
        }
    }
    
    public function onPreLogin(PlayerPreLoginEvent $e){
        $p = $e->getPlayer();
        $s = $this->plugin->getSession($p->getName());
        if($s != null){ //Conflict
            if($p->getClientSecret() == $s->getPlayer()->getClientSecret()){
                $this->plugin->getLogger()->write("error", $this->plugin->getTranslator()->translate("err.session", [$p->getName()]));
            }elseif($s->getState() == JamSession::STATE_AUTHED){
                $e->setCancelled();
                $this->plugin->getLogger()->write("error", $this->plugin->getTranslator()->translate("err.name", [$p->getName()]));
                $e->setKickMessage($this->plugin->getKitchen()->getFood("login.err.sameName"));
            }
        }
    }
    
    public function onJoin(PlayerJoinEvent $e){
        $this->plugin->startSession($e->getPlayer());
    }
    
    public function onCommand(PlayerCommandPreprocessEvent $e){
        $msg = $e->getMessage();
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() <= JamSession::STATE_PENDING){
            if($this->plugin->conf["command"]){
                $cmd = explode(" ", $msg)[0];
                if($cmd == "/login" || $cmd == "/register"){
                    return;
                }else{
                    $e->setCancelled();
                }
            }
            if($s->isRegistered()){
                $s->login($msg);
            }else{
                $s->direct($msg);
            }
        }
    }
    
    public function onQuit(PlayerQuitEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s != null){
            $s->logout();
        }
    }
    
    /*
     * Optional Listener
     */
    
    public function on_chat(PlayerChatEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
    
    public function on_dropitem(PlayerDropItemEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
    
    public function on_interact(PlayerInteractEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
    
    public function on_consume(PlayerItemConsumeEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
    
    public function on_move(PlayerMoveEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
    
    public function on_break(BlockBreakEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
}