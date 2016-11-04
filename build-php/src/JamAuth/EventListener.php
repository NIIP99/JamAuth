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

use JamAuth\JamAuth;
use JamAuth\Utils\JamSession;

class EventListener implements Listener{
    
    private $plugin;
    
    public function __construct(JamAuth $plugin){
	Server::getInstance()->getPluginManager()->registerEvents($this, $plugin);
        $this->plugin = $plugin;
    }
    
    public function onPlayerPreLogin(PlayerPreLoginEvent $e){
        $p = $e->getPlayer();
        $s = $this->plugin->getSession($p->getName());
        if($s != null){ //Conflict
            if($p->getClientSecret() == $s->getPlayer()->getClientSecret()){
                $this->plugin->getLogger()->log("error", $this->plugin->getTranslator()->translate("err.session", [$p->getName()]));
            }elseif($s->getState() == JamSession::STATE_AUTHED){
                $e->setCancelled();
                $this->plugin->getLogger()->log("error", $this->plugin->getTranslator()->translate("err.name", [$p->getName()]));
                $e->setKickMessage($this->plugin->getKitchen()->getFood("login.err.sameName"));
            }
        }
    }
    
    public function onJoin(PlayerJoinEvent $e){
        $this->plugin->startSession($e->getPlayer());
    }
    
    public function onChat(PlayerChatEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
    
    public function onDropItem(PlayerDropItemEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
    
    public function onInteract(PlayerInteractEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
    
    public function onConsume(PlayerItemConsumeEvent $e){
        $s = $this->plugin->getSession($e->getPlayer()->getName());
        if($s->getState() != JamSession::STATE_AUTHED){
            $e->setCancelled();
        }
    }
}