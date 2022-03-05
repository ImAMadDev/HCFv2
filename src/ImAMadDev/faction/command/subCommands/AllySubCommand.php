<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\faction\{FactionUtils, Faction};
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\{EOTWManager, ClaimManager};
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AllySubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("ally", "/faction ally (string: faction)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(EOTWManager::isEnabled() === true) {
			$sender->sendMessage(TextFormat::RED . "You cannot do this because End Of The World is enabled");
			return;
		}
		if(FactionUtils::MAXIMUM_ALLIES <= 0) {
			$sender->sendMessage(TextFormat::RED . "Alliances was disbaled!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
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
		$name = str_replace(" ", "_", $args[1]);
		$faction = $this->getMain()->getFactionManager()->getFaction($name);
		if($faction === null) {
			$sender->sendMessage(TextFormat::RED . "Faction {$name} doesn't exist!");
			return;
		}
		if(count($faction->getAllies()) >= FactionUtils::MAXIMUM_ALLIES) {
			$sender->sendMessage(TextFormat::RED . "You can't reach the maximum amount of allies per faction: " . FactionUtils::MAXIMUM_ALLIES);
			return;
		}
		if($faction->isAllying($sender->getFaction())) {
			$sender->getFaction()->addAlly($faction);
			$faction->addAlly($sender->getFaction());
			foreach($faction->getOnlineMembers() as $member) {
				$member->sendMessage(TextFormat::GRAY . "Your faction has allied with " . TextFormat::LIGHT_PURPLE . $sender->getFaction()->getName());
			}
			foreach($sender->getFaction()->getOnlineMembers() as $member) {
				$member->sendMessage(TextFormat::GRAY . "Your faction has allied with " . TextFormat::LIGHT_PURPLE . $faction->getName());
			}
		} else {
			$sender->getFaction()->addAllyRequest($faction);
			foreach($faction->getOnlineMembers() as $member) {
				$member->sendMessage(TextFormat::GREEN . $sender->getFaction()->getName() . TextFormat::GRAY . " has requested to ally with " . TextFormat::LIGHT_PURPLE . $faction->getName());
			}
			foreach($sender->getFaction()->getOnlineMembers() as $member) {
				$member->sendMessage(TextFormat::GREEN . $sender->getFaction()->getName() . TextFormat::GRAY . " has requested to ally with " . TextFormat::LIGHT_PURPLE . $faction->getName());
			}
		}
	}
}
