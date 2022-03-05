<?php

namespace ImAMadDev\events\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;

class DisableSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("disable", "/event disable (string: event)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$name = str_replace(" ", "_", TextFormat::clean($args[1]));
		if(!HCF::$EventsManager->eventExists($name)) {
			$sender->sendMessage(TextFormat::RED . "Evento {$name} no existe!");
			return;
		}
		HCF::$EventsManager->getEvent($name)->finish();
		$sender->sendMessage(TextFormat::GREEN . $name . " was deactivated!");
	}
}