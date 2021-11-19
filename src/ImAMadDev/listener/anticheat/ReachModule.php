<?php


namespace ImAMadDev\listener\anticheat;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\DiscordIntegration;

use pocketmine\item\{Item, ProjectileItem};
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\entity\Effect;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};

class ReachModule implements Listener {
	
	private static HCF $main;
	
	private static self $instance;
	
	public function __construct(HCF $main) {
		self::$main = $main;
		self::$instance = $this;
	}
	
	public static function getInstance() : self {
		return self::$instance;
	}
	
	public function onEntityDamageEvent(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			if($player instanceof HCFPlayer && $damager instanceof HCFPlayer) {
				$reach = round($damager->distance($player));
				if($reach > 6 && $damager->getInventory()->getItemInHand()->getId() !== Item::BOW && !$damager->getInventory()->getItemInHand() instanceof ProjectileItem && $damager->getGamemode() === 0 && !$damager->hasEffect(Effect::SPEED)) {
					DiscordIntegration::sendToDiscord("AntiCheat", $damager->getName() . " Suspect using Reach, Reach: " . $reach, DiscordIntegration::ALERT_WEBHOOK, "StaliaBot");
					$this->sendAlertToStaff($damager, $reach);
				}
			}
		}
	}
	
	public function sendAlertToStaff(HCFPlayer $cheater, int $reach) : void {
		foreach(self::$main->getServer()->getOnlinePlayers() as $player) {
			if($player->hasPermission('staff.alert') === true) {
				$player->sendMessage(TextFormat::RED . "AntiCheat > " . TextFormat::GRAY . $cheater->getName() . TextFormat::BLUE . " Suspect using Reach, Reach: " . TextFormat::RED . $reach);
			}
		}
	}
}