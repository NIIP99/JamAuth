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
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\Player;

use JamAuth\JamAuth;
use JamAuth\Utils\JamSession;

class EventListener implements Listener{
    
    private $plugin;
    
    public function __construct(JamAuth $plugin){
	Server::getInstance()->getPluginManager()->registerEvents($this, $plugin);
        $this->plugin = $plugin;
    }
    
    public function onPlayerPreLogin(PlayerPreLoginEvent $e){
        //Account check
    }
    
    public function onPlayerLogin(PlayerLoginEvent $e){
        $this->plugin->startSession($e->getPlayer());
    }
    
    public function onPlayerChat(PlayerChatEvent $e){
        
    }
}