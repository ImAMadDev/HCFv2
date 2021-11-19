<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;

class DisbandSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("disband", "/faction disband");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if($sender->getFaction() === null) {
			$sender->sendMessage(TextFormat::RED . "You must be in a faction to do this!");
			return;
		}
		if(!$sender->getFaction()->isLeader($sender->getName())) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if($sender->getFaction()->getDTR() <= 0){
			$sender->sendMessage(TextFormat::RED . "Your faction is now on DTR freeze!");
			return;
		}
		$name = $sender->getFaction()->getName();
		$sender->getFaction()->disband();
		$this->getServer()->broadcastMessage(TextFormat::DARK_AQUA . "Faction " . TextFormat::GOLD . $name . TextFormat::DARK_AQUA . " was successful deleted!");
	}
}