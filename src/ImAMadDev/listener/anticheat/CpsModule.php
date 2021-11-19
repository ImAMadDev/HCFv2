<?php


namespace ImAMadDev\listener\anticheat;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\DiscordIntegration;

use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;

class CpsModule implements Listener {

	private array $clicksData = [];
	
	private static HCF $main;
	
	private static self $instance;
	
	public function __construct(HCF $main) {
		self::$main = $main;
		self::$instance = $this;
	}
	
	public static function getInstance() : self {
		return self::$instance;
	}
	
	public function initPlayerClickData(HCFPlayer $player) : void {
		$this->clicksData[$player->getName()] = [time(), 0];
	}
	
	 public function getCPS(HCFPlayer $player) : int {
		if(!isset($this->clicksData[$player->getName()])) {
			return 0;
		}
		$time = $this->clicksData[$player->getName()][0];
		$clicks = $this->clicksData[$player->getName()][1];
		if ($time !== time()) {
			unset($this->clicksData[$player->getName()]);
			return 0;
		}
		return $clicks;
	}
	
	
	public function addClick(HCFPlayer $player) : void {
		if (!isset($this->clicksData[$player->getName()])) {
			$this->clicksData[$player->getName()] = [time(), 0];
		}
		$time = $this->clicksData[$player->getName()][0];
		$clicks = $this->clicksData[$player->getName()][1];
		if ($time !== time()) {
			$time = time();
			$clicks = 0;
		}
		$clicks++;
		$this->clicksData[$player->getName()] = [$time, $clicks];
	}
	
	public function removePlayerClickData(HCFPlayer $player) : void {
		unset($this->clicksData[$player->getName()]);
	}
	
	public function playerJoin(PlayerJoinEvent $event) : void {
		$this->initPlayerClickData($event->getPlayer());
	}
	
	public function playerQuit(PlayerQuitEvent $event) : void {
		$this->removePlayerClickData($event->getPlayer());
	}
	
	public function onDataPacketReceive(DataPacketReceiveEvent $event){
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        $check = false;
        if($packet instanceof LevelSoundEventPacket) {
			if($packet->sound == LevelSoundEvent::ATTACK_NODAMAGE) {
				$this->addClick($player);
                $check = true;
			}
		}
        if($packet instanceof InventoryTransactionPacket){
            $transactionType = $packet->trData;
            if($transactionType instanceof UseItemOnEntityTransactionData){
                $this->addClick($player);
                $check = true;
            }
        }
        if($check === true and $this->getCps($event->getOrigin()->getPlayer()) >= 25) {
        	$this->sendAlertToStaff($event->getOrigin()->getPlayer());
			DiscordIntegration::sendToDiscord("AntiCheat", $event->getOrigin()->getDisplayName() . " Suspect using AutoClick, CPS: " .$this->getCps($event->getOrigin()->getPlayer()), DiscordIntegration::ALERT_WEBHOOK, "StaliaBot");
		}
    }
    
    public function sendAlertToStaff(HCFPlayer $cheater) : void {
    	foreach(self::$main->getServer()->getOnlinePlayers() as $player) {
    		if($player->hasPermission('staff.alert') === true) {
    			$player->sendMessage(TextFormat::RED . "AntiCheat > " . TextFormat::GRAY . $cheater->getName() . TextFormat::BLUE . " Suspect using AutoClick, CPS: " . TextFormat::RED . $this->getCps($cheater));
    		}
    	}
    }
}