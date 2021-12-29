<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\faction\FactionUtils;
use JetBrains\PhpStorm\Pure;
use ImAMadDev\player\HCFPlayer;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class JoinSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("join", "/faction join <faction>");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$faction = $this->getMain()->getFactionManager()->getFaction($args[1]);
		if($faction === null) {
			$sender->sendMessage(TextFormat::RED . "Invalid faction!");
			return;
		}
		if(!$faction->isInvited($sender)) {
			$sender->sendMessage(TextFormat::RED . "You were not invited to {$faction->getName()}!");
			return;
		}
		if(count($faction->getMembers()) >= FactionUtils::MAXIMUM_MEMBERS) {
			$sender->sendMessage(TextFormat::RED . $faction->getName() . " has reached its max amount of members!");
			return;
		}
		if($sender->getFaction() !== null) {
			$sender->sendMessage(TextFormat::RED . "You must leave your faction to do this!");
			return;
		}
        $sender->getCache()->setInData('faction', $faction->getName());
        $sender->getCache()->saveData();
        $sender->setFaction($faction);
		$faction->addMember($sender);
		$faction->message(TextFormat::GREEN . $sender->getName() . " has joined the faction.");
	}
}