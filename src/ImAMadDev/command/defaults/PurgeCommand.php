<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\Command;
use ImAMadDev\manager\PurgeManager;

class PurgeCommand extends Command {
	
	public function __construct() {
		parent::__construct("purge", "Purge manager.", "/purge [on - off]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission('purge.manager')) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if($args[0] == "on"){
			if(PurgeManager::isEnabled()) {
				$sender->sendMessage(TextFormat::RED . "Purge is already enabled!");
				return;
			}
			PurgeManager::set(true, 30);
			$sender->sendMessage(TextFormat::GREEN . "You have enabled Purge!");
		}
		if($args[0] == "off"){
			if(!PurgeManager::isEnabled()) {
				$sender->sendMessage(TextFormat::RED . "Purge is already disabled!");
				return;
			}
			PurgeManager::set(false, 30);
			$sender->sendMessage(TextFormat::GREEN . "You have disabled Purge!");
		}
	}
}