<?php

namespace ImAMadDev\koth\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\koth\ticks\AutomaticKOTHTick;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;

class DisableSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("disable", "/koth disable [string: arena]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if($sender->hasPermission('koth.manager') === false) {
			$sender->sendMessage(TextFormat::RED . "You don't have permission!");
			return;
		}
		if(HCF::$KOTHManager->getSelected() === null) {
			$sender->sendMessage(TextFormat::RED . "No arena activate!");
			return;
		}
		HCF::$KOTHManager->getSelected()->finish();
		HCF::$KOTHManager->selectArena();
		$sender->sendMessage(TextFormat::GREEN . HCF::$KOTHManager->getSelected()->getName() . " was deactivated!");
	}
}