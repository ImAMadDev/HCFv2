<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\HCF;
use ImAMadDev\command\SubCommand;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;

class TopSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("top", "/faction top");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		$sender->sendMessage(HCF::$factionManager->getTopPower());
	}
}