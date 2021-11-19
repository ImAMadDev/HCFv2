<?php

namespace ImAMadDev\ability\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\AbilityManager;

class ListSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("list", "/ability list");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		$names = [];
		foreach(AbilityManager::getInstance()->getAbilities() as $ability) {
			$names[] = $ability->getName();
		}
		$sender->sendMessage(TextFormat::GREEN . "Abilities: " . TextFormat::GOLD . implode(", ", $names));
    }

}