<?php

namespace ImAMadDev\koth\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;

class SaveSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("save", "/koth save");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!HCF::$KOTHManager->creatorExists($sender)) {
			$sender->sendMessage(TextFormat::RED . "You don't have session!");
			return;
		}
		HCF::$KOTHManager->addArena($sender);
    }
}