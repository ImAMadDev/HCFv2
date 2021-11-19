<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;

class InfoSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("info", "/faction info [faction]", ["who", "show"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[1])) {
			if($sender->getFaction() === null) {
				$sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
				return;
			}
			$faction = $sender->getFaction();
		} else {
			$faction = $this->getMain()->getFactionManager()->getFaction($args[1]);
			if($faction === null) {
				$sender->sendMessage(TextFormat::RED . "This faction doesn't exist");
				return;
			}
		}	
		$sender->sendMessage($faction->getInformationString());
	}
}