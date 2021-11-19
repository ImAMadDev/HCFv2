<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use JetBrains\PhpStorm\Pure;
use ImAMadDev\player\{PlayerData, HCFPlayer};
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class KickSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("kick", "/faction kick <player>");
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
		if($sender->getFaction()->isLeader($sender->getName()) === false && $sender->getFaction()->isColeader($sender->getName()) === false) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do!");
			return;
		}
		$name = $this->getServer()->getPlayerByPrefix($args[1]) !== null ? $this->getServer()->getPlayerByPrefix($args[1])->getName() : $args[1];
		if(!$sender->getFaction()->isInFaction($name)) {
			$sender->sendMessage(TextFormat::RED . "Invalid Player!");
			return;
		}
        if($sender->getFaction()->isLeader($name)) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if($this->getServer()->getPlayerByPrefix($name) instanceof HCFPlayer) {
			if($sender->getFaction()->isInFaction($name)) {
				$this->getServer()->getPlayerByPrefix($name)->setFaction(null);
			}
		} 
		if($sender->getFaction()->isInFaction($name)) {
			PlayerData::setData($name, 'faction', null);
		}
		$sender->getFaction()->message(TextFormat::GREEN . $name . " has left the faction.");
		$sender->getFaction()->removeMember($name);
	}
}