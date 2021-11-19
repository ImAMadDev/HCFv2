<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;

class ListSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("list", "/faction list [page]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		$factions = [];
		foreach($this->getMain()->getFactionManager()->getFactions() as $faction) {
			$factions[] = $faction;
		}
		if(!arsort($factions)) {
			$sender->sendMessage(TextFormat::RED . "Error Occurred!");
			return;
		}
		$page = 1;
		if(isset($args[1])) {
			$page = (int)$args[1];
		}
		$pages = ceil(count($factions) / 10);
		if((!is_numeric($page)) or $page > $pages) {
			$sender->sendMessage(TextFormat::RED . "Invalid Page!");
			return;
		}
		$factions = array_slice($factions, ($page - 1) * 10, 10);
		$sender->sendMessage(TextFormat::DARK_GREEN . TextFormat::BOLD . "FACTIONS LIST " . TextFormat::RESET . TextFormat::GRAY . "($page/$pages)");
		$place = (($page - 1) * 10) + 1;
		foreach($factions as $faction) {
		    if(count($faction->getOnlineMembers()) == 0) continue;
			$sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . "$place. " . TextFormat::RESET . TextFormat::YELLOW . $faction->getName() . TextFormat::DARK_GRAY . " | " . TextFormat::GOLD . count($faction->getOnlineMembers()) . " online" . TextFormat::YELLOW . " {$faction->getDTR()} DTR");
			++$place;
		}
	}
}