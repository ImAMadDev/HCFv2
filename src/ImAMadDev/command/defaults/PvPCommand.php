<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\Command;
use ImAMadDev\manager\SOTWManager;
use ImAMadDev\player\HCFPlayer;

class PvPCommand extends Command {
	
	public function __construct() {
		parent::__construct("pvp", "PvP manager.", "/pvp [on - off]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if($args[0] == "enable"){
			if($sender->isInvincible() === false) {
				$sender->sendMessage(TextFormat::RED . "Your pvp timer is already disabled");
				return;
			}
			$sender->setInvincible(0);
			$sender->sendMessage(TextFormat::GREEN . "You have disabled your pvp timer!");
		}
	}
}