<?php

namespace ImAMadDev\koth\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\faction\Faction;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;
class DisbandSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("disband", "/koth disband (string: arena)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if($sender->isOp() === false) {
			$sender->sendMessage(TextFormat::RED . "You don't have permission!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		$name = str_replace(" ", "_", TextFormat::clean($args[1]));
		if(!HCF::$KOTHManager->arenaExists($name)) {
			$sender->sendMessage(TextFormat::RED . "KOTH {$name} don't exist");
			return;
		}
		$this->getMain()->getKOTHManager()->removeArena($name);
		$sender->sendMessage(TextFormat::AQUA . $name. " was deleted");
	}
}