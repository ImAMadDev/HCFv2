<?php

namespace ImAMadDev\events\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;
use ImAMadDev\utils\HCFUtils;

class SetTimeSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("settime", "/event settime (string: time, example: 1d,1h,1m)");
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
		$time = HCFUtils::strToSeconds($args[1]);
		$sender->sendMessage(TextFormat::GREEN . HCF::$EventsManager->setTime($time, $sender));
    }
}