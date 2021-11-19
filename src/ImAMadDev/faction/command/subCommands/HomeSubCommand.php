<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\faction\ticks\TeleportHomeTask;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\{ClaimManager, SOTWManager};
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\utils\TextFormat;

class HomeSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("home", "/faction home");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if($sender->getFaction() === null) {
			$sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
			return;
		}
		if($sender->getFaction()->getHome() === null) {
			$sender->sendMessage(TextFormat::RED . "Your faction home is not set!");
			return;
		}
		$time = 15;
		if($sender->isInvincible() and SOTWManager::isEnabled() === false) {
			$sender->sendMessage(TextFormat::RED . "You may not enter here while your pvp timer is active!");
			return;
		}
		$claim = ClaimManager::getInstance()->getClaimByPosition($sender->getPosition()) == null ? null : ClaimManager::getInstance()->getClaimByPosition($sender->getPosition());
		$claimName = ClaimManager::getInstance()->getClaimByPosition($sender->getPosition()) == null ? "Wilderness" : ClaimManager::getInstance()->getClaimByPosition($sender->getPosition())->getName();
		if(stripos($claimName, "Spawn") !== false) {
			$home = $sender->getFaction()->getHome();
			$sender->teleport($home);
			$sender->sendMessage(TextFormat::GREEN . "You have successfully teleport to your home location.");
			$sender->getWorld()->addSound($sender->getPosition(), new EndermanTeleportSound());
			return;
		}
		if($claim !== null) {
			if($claim->isFaction() and $claim->getFaction()->isInFaction($sender->getName()) === false) {
				$time = 20;
			}
			if($claim->isFaction() and $claim->getFaction()->isInFaction($sender->getName()) === true) {
				$time = 10;
			}
		}
		if($sender->getCooldown()->has('combattag')) {
			$sender->sendMessage(TextFormat::RED . "You're combat tagged!");
			return;
		}
		if($sender->getCooldown()->has('home_teleport')) {
			$sender->sendMessage(TextFormat::RED . "Already in home cooldown!");
			return;
		}
		if($sender->getCooldown()->has('stuck_teleport')) {
			$sender->sendMessage(TextFormat::RED . "Already in stuck cooldown!");
			return;
		}
		if($sender->getCooldown()->has('logout')) {
			$sender->sendMessage(TextFormat::RED . "Already in logout cooldown!");
			return;
		}
        $sender->getCooldown()->add('home_teleport', $time);
        $this->getMain()->getScheduler()->scheduleRepeatingTask(new TeleportHomeTask($sender), 20);
    }
}