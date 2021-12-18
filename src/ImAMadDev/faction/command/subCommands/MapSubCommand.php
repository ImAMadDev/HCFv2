<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use JetBrains\PhpStorm\Pure;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\claim\Claim;
use ImAMadDev\manager\ClaimManager;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class MapSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("map", "/faction map");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
        $sender->getClaimView()->setUpdate(true);
        $sender->sendMessage(TextFormat::GRAY . "Now you are seeing all the claims");
	}
}