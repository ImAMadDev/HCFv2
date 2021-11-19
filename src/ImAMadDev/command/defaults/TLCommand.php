<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TLCommand extends Command {
	
	public function __construct() {
		parent::__construct("tl", "/tl");
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
		$pos = round($sender->getPosition()->getFloorX()) . ":" . round($sender->getPosition()->getFloorY()) . ":" . round($sender->getPosition()->getFloorZ());
		$sender->getFaction()->message(TextFormat::colorize("&l&c[!]&r&7 {$sender->getName()} current location: &l&7$pos"));
	}
}