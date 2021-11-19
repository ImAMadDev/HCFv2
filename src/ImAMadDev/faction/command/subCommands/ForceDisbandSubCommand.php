<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\HCF;

class ForceDisbandSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("forcedisband", "/faction forcedisband (string: faction)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("faction.manager")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[1])){
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if(HCF::$factionManager->getFaction($args[1]) === null) {
			$sender->sendMessage(TextFormat::RED . "Faction $args[1] no existe");
			return;
		}
		$name = HCF::$factionManager->getFaction($args[1])->getName();
		HCF::$factionManager->getFaction($args[1])->disband();
		$this->getServer()->broadcastMessage(TextFormat::DARK_AQUA . "Faction " . TextFormat::GOLD . $name . TextFormat::DARK_AQUA . " was successful deleted!");
	}
}