<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RollbackCommand extends Command {
	
	public function __construct() {
		parent::__construct("rollback", "Manage rollbacks.", "/rollback <player> ");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission('rollback.command')) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(count($args) < 1) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$name = array_shift($args);
		$player = $this->getServer()->getPlayerByPrefix($name);
		if($player === null) {
			$sender->sendMessage(TextFormat::RED . "Invalid Player!");
			return;
		}
		$player->restoreInventory();
		$sender->sendMessage(TextFormat::GRAY . "You've restore the inventory of " . TextFormat::GREEN . $player->getName());
		$player->sendMessage(TextFormat::GRAY . "Your inventory has restored thanks to " . TextFormat::GREEN . $sender->getName());
	}
}