<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnallySubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("unally", "/faction unally <faction>");
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
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$faction = $this->getMain()->getFactionManager()->getFaction($args[1]);
		if($faction === null) {
			$sender->sendMessage(TextFormat::RED . "Faction $args[1] doesn't exist!");
			return;
		}
		$sender->getFaction()->message(TextFormat::GRAY . "Your faction has unallied with ". TextFormat::GREEN . $faction->getName());
		$faction->message(TextFormat::GRAY . "Your faction has unallied with ". TextFormat::GREEN . $sender->getFaction()->getName());
		$faction->removeAlly($sender->getFaction()->getName());
		$sender->getFaction()->removeAlly($faction->getName());
	}
}