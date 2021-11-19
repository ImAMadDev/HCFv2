<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\ClaimManager;

use JetBrains\PhpStorm\Pure;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class SetHomeSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("sethome", "/faction sethome");
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
		$claim = ClaimManager::getInstance()->getClaimNameByPosition($sender->getPosition());
		if($claim !== $sender->getFaction()->getName()) {
			$sender->sendMessage(TextFormat::RED . "You must be in your claim to do this action!");
			return;
		}
		$sender->getFaction()->setHome($sender->getPosition());
		$sender->getFaction()->message(TextFormat::GREEN . "Faction home has been set by " . TextFormat::GOLD . $sender->getName());
	}
}