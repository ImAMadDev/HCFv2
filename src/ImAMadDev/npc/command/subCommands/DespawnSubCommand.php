<?php

namespace ImAMadDev\npc\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\EntityManager;

class DespawnSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("despawn", "/npc despawn (string: name)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		EntityManager::despawn($sender, $args[1]);
    }
}