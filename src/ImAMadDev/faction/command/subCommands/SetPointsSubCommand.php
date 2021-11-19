<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;

class SetPointsSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("setpoints", "/faction setpoints (string: faction) (int: points)");
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
			$points = intval($args[2]);
			$faction = $this->getMain()->getFactionManager()->getFaction($args[1]);
			if($faction === null) {
				$sender->sendMessage(TextFormat::RED . "This faction doesn't exist");
				return;
			}
			if($points < $faction->getPoints()) {
				$faction->removePoints($points);
				$sender->sendMessage(TextFormat::GREEN . "You have reduce $points of Points to the faction {$faction->getName()}");
			} elseif($points >= $faction->getPoints()) {
				$faction->addPoints($points);
				$sender->sendMessage(TextFormat::GREEN . "You have add $points of Points to the faction {$faction->getName()}");
			}
		}
	}

}