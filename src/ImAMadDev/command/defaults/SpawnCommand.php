<?php

namespace ImAMadDev\command\defaults;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use ImAMadDev\command\Command;
use ImAMadDev\manager\{SOTWManager, ClaimManager};
use ImAMadDev\player\HCFPlayer;

class SpawnCommand extends Command {
	
	public function __construct() {
		parent::__construct("spawn", "Return to spawn.", "/spawn");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage("No permission");
			return;
		}
		$claim = ClaimManager::getInstance()->getClaimNameByPosition($sender->getPosition());
		if(stripos($claim, "Spawn") !== false or SOTWManager::isEnabled()) {
			$sender->teleport($sender->getWorld()->getSpawnLocation());
			$sender->sendMessage(TextFormat::GREEN . "Teleported Spawn");
		} else {
			$sender->sendMessage(TextFormat::RED . "You aren't in the spawn!");
		}
	}
}