<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;

class SetDTRSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("setdtr", "/faction setdtr (string: faction) (int: DTR)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("faction.manager")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
		} else {
			if(!isset($args[2])) {
				$sender->sendMessage(TextFormat::RED . $this->getUsage());
				return;
			}
			if(!is_numeric($args[2])) {
				$sender->sendMessage(TextFormat::RED . $this->getUsage());
				return;
			}
			$dtr = intval($args[2]);
			$faction = $this->getMain()->getFactionManager()->getFaction($args[1]);
			if($faction === null) {
				$sender->sendMessage(TextFormat::RED . "This faction doesn't exist");
				return;
			}
			if($dtr > $faction->getMaxDTR()) {
				$sender->sendMessage(TextFormat::RED . "You can't add $dtr of DTR to the faction {$faction->getName()} because $dtr reach the maximum amount of DTR {$faction->getDTR()} ");
				return;
			}
			if($dtr < $faction->getDTR()) {
				$faction->removeDTR($dtr);
				$sender->sendMessage(TextFormat::GREEN . "You have reduce $dtr of DTR to the faction {$faction->getName()}");
			} elseif($dtr >= $faction->getDTR()) {
				$faction->addDTR($dtr);
				$sender->sendMessage(TextFormat::GREEN . "You have add $dtr of DTR to the faction {$faction->getName()}");
			}
		}
	}

}