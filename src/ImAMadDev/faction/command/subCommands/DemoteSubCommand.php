<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DemoteSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("demote", "/faction demote <player>");
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
        $player = $this->getServer()->getPlayerByPrefix($args[1]);
        if(!$player instanceof HCFPlayer) {
            $sender->sendMessage(TextFormat::RED . "Player isn't online");
            return;
        }
        if(!$sender->getFaction()->isInFaction($player->getName())) {
            $sender->sendMessage(TextFormat::RED . $player->getName() . " is not one of your faction members!");
            return;
        }
        if($sender->getFaction()->isMember($player->getName())) {
			$sender->sendMessage(TextFormat::RED . "This player can't be demoted!");
			return;
		}
		if($sender->getFaction()->isColeader($player->getName())) {
			$sender->getFaction()->addMember($player);
			$sender->getFaction()->message(TextFormat::GREEN . $player->getName() . TextFormat::GRAY . " Demoted from Coleader to Member by " . TextFormat::LIGHT_PURPLE . $sender->getName());
		}
    }
}