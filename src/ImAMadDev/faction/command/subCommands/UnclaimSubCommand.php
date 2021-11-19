<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;

use JetBrains\PhpStorm\Pure;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class UnclaimSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("unclaim", "/faction unclaim ");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if($sender->getFaction() === null) {
			$sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
			return;
		}
		if(!$sender->getFaction()->isLeader($sender->getName())) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!$sender->getFaction()->hasClaim()) {
			$sender->sendMessage(TextFormat::RED . "Your faction don't have claim!");
			return;
		}
		$sender->getFaction()->unClaim();
		$sender->getFaction()->setHome();
		$sender->sendMessage(TextFormat::GREEN . "You've successfully unclaimed this part of the land!");
    }
}