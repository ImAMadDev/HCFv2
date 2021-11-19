<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;

class SetBalanceSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("setbalance", "/faction setbalance (string: faction) (int: balance)");
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
			$balance = intval($args[2]);
			$faction = $this->getMain()->getFactionManager()->getFaction($args[1]);
			if($faction === null) {
				$sender->sendMessage(TextFormat::RED . "This faction doesn't exist");
				return;
			}
			if($balance < $faction->getBalance()) {
				$faction->removeBalance($balance);
				$sender->sendMessage(TextFormat::GREEN . "You have reduce $balance of Balance to the faction {$faction->getName()}");
			} elseif($balance >= $faction->getBalance()) {
				$faction->addBalance($balance);
				$sender->sendMessage(TextFormat::GREEN . "You have add $balance of Balance to the faction {$faction->getName()}");
			}
		}
	}

}