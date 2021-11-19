<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class LeaderSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("leader", "/faction leader <player>");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if($sender->getFaction() === null) {
			$sender->sendMessage(TextFormat::RED . "You must be in a faction to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$player = $this->getServer()->getPlayerByPrefix($args[1]);
		if(!$player instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "Player isn't online");
			return;
		}
		if((!$player->getFaction()->isInFaction($sender)) or $player->getFaction() === null) {
			$sender->sendMessage(TextFormat::RED . "Player isn't in your faction!");
			return;
		}
		if(!$sender->getFaction()->isLeader($sender->getName())) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		$sender->getFaction()->addColeader($sender, $player);
		$sender->getFaction()->message(TextFormat::GREEN . $player->getName() . TextFormat::GRAY . " has been promoted to " . TextFormat::GOLD . "Leader");
	}
}