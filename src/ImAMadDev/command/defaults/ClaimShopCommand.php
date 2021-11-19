<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\Command;
use ImAMadDev\player\{PlayerData, HCFPlayer};

class ClaimShopCommand extends Command {
	
	public function __construct() {
		parent::__construct("claimshop", "Reclaim command.", "/claimshop");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(empty(PlayerData::getSavedItems($sender->getName()))) {
			$sender->sendMessage(TextFormat::RED . "It appears there is no claim found for you!");
			return;
		}
		$sender->claimBought();
		$this->getServer()->broadcastMessage(TextFormat::colorize("&c{$sender->getName()} &7Luis has claimed his store-bought rewards &e/claimshop"));
	}
}