<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class PromoteSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("promote", "/faction promote <player>");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
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
		$player = $this->getServer()->getPlayerByPrefix($args[1]);
		if(!$player instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "invalid Player!");
			return;
		}
		if(!$sender->getFaction()->isInFaction($player->getName())) {
			$sender->sendMessage(TextFormat::RED . $player->getName() . " is not one of your faction members!");
			return;
		}
		if($sender->getFaction()->isLeader($player->getName())) {
			$sender->sendMessage(TextFormat::RED . $player->getName() . " can not be promoted!");
			return;
		}
		if($sender->getFaction()->isCoLeader($player->getName())) {
			$sender->sendMessage(TextFormat::RED . $player->getName() . " can not be promoted!");
			return;
		}
		$sender->getFaction()->message(TextFormat::GREEN . $player->getName() . TextFormat::GRAY . " has been promoted to " . TextFormat::LIGHT_PURPLE . $sender->getName());
		$sender->getFaction()->addCoLeader($player);
	}
}