<?php

namespace ImAMadDev\koth\command\subCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;

class SetPosSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("setpos", "/koth setpos (int: pos)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!HCF::$KOTHManager->creatorExists($sender)) {
			$sender->sendMessage(TextFormat::RED . "You don't have session!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		if(!is_numeric($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		if($args[1] == 1) {
			$sender->sendMessage(TextFormat::GREEN . HCF::$KOTHManager->setPos(1, $sender));
		} elseif($args[1] == 2) {
			$sender->sendMessage(TextFormat::GREEN . HCF::$KOTHManager->setPos(2, $sender));
		} else {
			$sender->sendMessage($this->getUsage());
		}
    }
}