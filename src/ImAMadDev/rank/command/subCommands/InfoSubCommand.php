<?php

namespace ImAMadDev\rank\command\subCommands;

use ImAMadDev\command\SubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class InfoSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("info", "/rank info (string: rank)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		if(!$this->getMain()->getRankManager()->isRank($args[1])) {
			$sender->sendMessage("This rank {$args[1]} doesn't exist!");
			return;
		}
		$rank = $this->getMain()->getRankManager()->getRank($args[1]);
		$permissions = $rank->getPermissions();
		$sender->sendMessage(TextFormat::DARK_GREEN . TextFormat::BOLD . "Rank {$rank->getName()} Information ");
		$sender->sendMessage(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Format: {$rank->getFormat()} ");
		$sender->sendMessage(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Name Tag: {$rank->getTag()} ");
		$sender->sendMessage(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Permissions: ". implode(", ", $rank->getPermissions()));
	}
}
?>