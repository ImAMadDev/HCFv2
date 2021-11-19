<?php

namespace ImAMadDev\koth\command\subCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\koth\ticks\AutomaticKOTHTick;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;

class EnableSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("enable", "/koth enable [string: arena]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if($sender->isOp() === false) {
			$sender->sendMessage(TextFormat::RED . "You don't have permission!");
			return;
		}
		if(!isset($args[1])) {
			new AutomaticKOTHTick(HCF::getInstance(), (20 * 30));
			$sender->sendMessage(TextFormat::RED . "Un nuevo KoTH se activara en 30 segundos!");
			return;
		}
		$name = str_replace(" ", "_", TextFormat::clean($args[1]));
		if(!HCF::$KOTHManager->arenaExists($name)) {
			$sender->sendMessage(TextFormat::RED . "KOTH {$name} don't exist");
			return;
		}
		$this->getMain()->getKOTHManager()->selectArena($name);
		$sender->sendMessage(TextFormat::AQUA . $name. " was activate");
	}
}