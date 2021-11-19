<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;

class UnFocusSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("unfocus", "/faction unfocus");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		$sender->setFocus();
		$sender->sendMessage(TextFormat::GRAY . "Now you're unfocusing ");
	}
}