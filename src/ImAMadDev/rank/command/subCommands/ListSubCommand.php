<?php

namespace ImAMadDev\rank\command\subCommands;

use ImAMadDev\command\SubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("list", "/rank list [page]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		$ranks = [];
		foreach($this->getMain()->getRankManager()->getRanks() as $rank) {
			$ranks[] = $rank;
		}
		if(!arsort($ranks)) {
			$sender->sendMessage("errorOccurred");
			return;
		}
		$page = 1;
		if(isset($args[1])) {
			$page = (int)$args[1];
		}
		$pages = ceil(count($ranks) / 10);
		if((!is_numeric($page)) or $page > $pages) {
			$sender->sendMessage("invalidPage");
			return;
		}
		$ranks = array_slice($ranks, ($page - 1) * 10, 10);
		$sender->sendMessage(TextFormat::DARK_GREEN . TextFormat::BOLD . "Rank List " . TextFormat::RESET . TextFormat::GRAY . "($page/$pages)");
		$place = (($page - 1) * 10) + 1;
		foreach($ranks as $rank) {
			$sender->sendMessage(TextFormat::BOLD . TextFormat::GREEN . "$place. " . TextFormat::RESET . TextFormat::YELLOW . $rank->getName() . TextFormat::DARK_GRAY . " | " . TextFormat::GOLD . "format: " . TextFormat::colorize($rank->getFormat()) . " - NameTag: " . TextFormat::colorize($rank->getTag()));
			++$place;
		}
	}
}
?>