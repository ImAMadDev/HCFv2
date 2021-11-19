<?php

namespace ImAMadDev\koth\command\subCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\faction\{FactionUtils, Faction};
use ImAMadDev\player\{PlayerData, HCFPlayer};

class InfoSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("info", "/koth info (string: faction)", ["who", "show"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		} else {
			$sender->sendMessage($this->getMain()->getKOTHManager()->getKoth($args[1]));
		}
	}
}