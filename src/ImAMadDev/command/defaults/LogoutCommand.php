<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;
use pocketmine\lang\KnownTranslationKeys;
use ImAMadDev\manager\{SOTWManager, ClaimManager};
use ImAMadDev\ticks\player\LogoutTask;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class LogoutCommand extends Command {
	
	public function __construct() {
		parent::__construct("logout", "Leave safety from the server.", "/logout ", ["leave", "hub"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		$claim = ClaimManager::getInstance()->getClaimByPosition($sender->getPosition()) == null ? "Wilderness" : ClaimManager::getInstance()->getClaimByPosition($sender->getPosition())->getProperties()->getName();
		if(stripos($claim, "Spawn") !== false or SOTWManager::isEnabled()) {
			$sender->setCanLogout(true);
            $sender->kick(KnownTranslationKeys::DISCONNECTIONSCREEN_NOREASON);
		} else {
			if($sender->getCooldown()->has('combattag')) {
				$sender->sendMessage(TextFormat::RED . "You're combat tagged!");
				return;
			}
			if($sender->getCooldown()->has('home_teleport')) {
				$sender->sendMessage(TextFormat::RED . "Already in home telepor cooldown!");
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
			$sender->sendMessage(TextFormat::GREEN . "Logout cooldown has started!");
			$sender->getCooldown()->add('logout', 30);
			$this->getMain()->getScheduler()->scheduleRepeatingTask(new LogoutTask($sender), 20);
		}
	}
}