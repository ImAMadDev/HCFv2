<?php

namespace ImAMadDev\events\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;

class SetScoreboardSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("setscoreboard", "/event setscoreboard (string: formated, uss & for colored it)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!HCF::$EventsManager->creatorExists($sender)) {
			$sender->sendMessage(TextFormat::RED . "You don't have session!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		array_shift($args);
		$tag = implode(" ", $args);
		$sender->sendMessage(TextFormat::GREEN . HCF::$EventsManager->setScoreboard($tag, $sender));
    }
}