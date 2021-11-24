<?php

namespace ImAMadDev\koth\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;

use ImAMadDev\command\SubCommand;

class InfoSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("info", "/koth info (string: faction)", ["who", "show"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
		} else {
			$sender->sendMessage($this->getMain()->getKOTHManager()->getKoth($args[1]));
		}
	}
}