<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\HCF;
use JetBrains\PhpStorm\Pure;
use ImAMadDev\player\HCFPlayer;
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
		if($sender->getFaction()->isLeader($sender->getName()) === false && $sender->getFaction()->isCoLeader($sender->getName()) === false) {
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
        $player = $this->getServer()->getPlayerByPrefix($name);
        if($sender->getFaction()->isInFaction($name)) {
            HCF::getInstance()->getCache($name)?->setInData('faction', null);
            //HCF::getInstance()->getCache($name)?->loadFactionRank();
        }
		if($player instanceof HCFPlayer) {
			if($sender->getFaction()->isInFaction($name)) {
				$player->setFaction(null);
			}
		}
		$sender->getFaction()->message(TextFormat::GREEN . $name . " has left the faction.");
		$sender->getFaction()->removeMember($name);
	}
}