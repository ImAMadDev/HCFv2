<?php

namespace ImAMadDev\events\command\subCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\{PlayerData, HCFPlayer};
use ImAMadDev\HCF;
use ImAMadDev\utils\HCFUtils;

class AddCommandSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("addcommand", "/event addcommand (string: command)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!HCF::$EventsManager->creatorExists($sender)) {
			$sender->sendMessage(TextFormat::RED . "You don't have session!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		array_shift($args);
		$command = implode(" ", $args);
		$sender->sendMessage(TextFormat::GREEN . HCF::$EventsManager->addCommand($command, $sender));
    }
}