<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\Command;
use ImAMadDev\manager\EOTWManager;

class EOTWCommand extends Command {
	
	public function __construct() {
		parent::__construct("eotw", "End of the world manager.", "/eotw [on - off]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission('eotw.manager')) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if($args[0] == "on"){
			if(EOTWManager::isEnabled()) {
				$sender->sendMessage(TextFormat::RED . "Start of the world is already enabled!");
				return;
			}
			EOTWManager::set(true, 120);
			$sender->sendMessage(TextFormat::GREEN . "You have enabled Start of the world!");
		}
		if($args[0] == "off"){
			if(!EOTWManager::isEnabled()) {
				$sender->sendMessage(TextFormat::RED . "Start of the world is already disabled!");
				return;
			}
			EOTWManager::set(false, 120);
			$sender->sendMessage(TextFormat::GREEN . "You have disabled Start of the world!");
		}
	}
}