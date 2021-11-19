<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\Command;

class SetBalanceCommand extends Command {
	
	public function __construct() {
		parent::__construct("setbalance", "/setbalance (string: player) (int: balance)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("balance.manager")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
		} else {
			if(!isset($args[1])) {
				$sender->sendMessage(TextFormat::RED . $this->getUsage());
				return;
			}
			if(!is_numeric($args[1])) {
				$sender->sendMessage(TextFormat::RED . $this->getUsage());
				return;
			}
			$balance = intval($args[1]);
			$player = $this->getServer()->getPlayerByPrefix($args[0]);
			if($player === null) {
				$sender->sendMessage(TextFormat::RED . "This player doesn't exist");
				return;
			}
			$player->setBalance($balance);
			$sender->sendMessage(TextFormat::GREEN . "You have set $balance of Balance to the player {$player->getName()}");
		}
	}

}