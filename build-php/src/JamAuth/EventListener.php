<?php
namespace JamAuth;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\server\ServerCommandEvent;

use JamAuth\JamAuth;

class EventListener implements Listener{
    
    private $plugin;
    
    public function __construct(JamAuth $plugin){
	Server::getInstance()->getPluginManager()->registerEvents($this, $plugin);
        $this->plugin = $plugin;
    }
    
    public function onPlayerJoin(PlayerJoinEvent $e){
        
    }
    
    public function onPlayerChat(PlayerChatEvent $e){
        
    }
}