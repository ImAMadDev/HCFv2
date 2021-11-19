<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\Command;

class ShopCommand extends Command {
	
	public function __construct() {
		parent::__construct("shop", "Console Command.", "/shop (message)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("broadcast.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(count($args) === 0) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$message = implode(" ", $args);
		$this->getServer()->broadcastMessage($message);
	}
}