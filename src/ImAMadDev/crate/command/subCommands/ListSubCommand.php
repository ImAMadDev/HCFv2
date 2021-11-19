<?php

namespace ImAMadDev\crate\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\CrateManager;

class ListSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("list", "/crate list");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		$names = [];
		foreach(CrateManager::getInstance()->getCrates() as $crate) {
			$names[] = $crate->getName();
		}
		$sender->sendMessage(TextFormat::GREEN . "Keys: " . TextFormat::GOLD . implode(", ", $names));
    }

}