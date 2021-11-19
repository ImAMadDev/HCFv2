<?php

namespace ImAMadDev\kit\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\{PlayerData, HCFPlayer};
use ImAMadDev\manager\KitManager;
use ImAMadDev\kit\Kit;

class ResetSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("reset", "/kit reset (string: player name) (string: kit name)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("resetkit.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[1]) or !isset($args[2])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		if(($kit = KitManager::getInstance()->getKitByName($args[2])) instanceof Kit) {
			if(($player = $this->getServer()->getPlayerByPrefix($args[1])) instanceof HCFPlayer) {
				PlayerData::setCountdown($player->getName(), $kit->getName(), 0);
				$sender->sendMessage(TextFormat::GREEN . "You've reset the kit {$kit->getName()}'s countdowns from the player {$player->getName()}!");
				$player->sendMessage(TextFormat::GREEN . "Your kit {$kit->getName()}'s countdowns has been reloaded by {$sender->getName()}!");
            } else {
				$sender->sendMessage(TextFormat::RED . "Error this player doesn't exist!");
            }
        } else {
			$sender->sendMessage(TextFormat::RED . "Error this kit doesn't exist!");
		}
    }

}