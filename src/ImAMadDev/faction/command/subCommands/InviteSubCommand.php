<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\faction\FactionUtils;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class InviteSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("invite", "/faction invite <player>");
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
		if(count($sender->getFaction()->getAllMembers()) >= FactionUtils::MAXIMUM_MEMBERS) {
			$sender->sendMessage(TextFormat::RED . $sender->getFaction()->getName() . " has reached its max amount of members!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$player = $this->getServer()->getPlayerByPrefix($args[1]);
		if(!$player instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "Invalid player!");
			return;
		}
		if($sender->getFaction()->isInvited($player)) {
			$sender->sendMessage(TextFormat::RED . "You already have invited this player!");
			return;
		}
		$sender->getFaction()->addInvite($player);
		$sender->sendMessage(TextFormat::GREEN . "You've invited {$player->getName()} to the faction!");
		$player->sendMessage(TextFormat::GREEN . "You've been invited to {$sender->getFaction()->getName()}, use /f join {$sender->getFaction()->getName()} to join!");
	}
}