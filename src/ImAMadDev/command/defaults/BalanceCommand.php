<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;

class BalanceCommand extends Command {
	
	public function __construct() {
		parent::__construct("balance", "Balance manager.", "/balance [player]", ["bal"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TextFormat::GREEN . "Current balance " . TextFormat::GOLD . $sender->getBalance());
		} else {
			if(($player = $this->getServer()->getPlayerByPrefix($args[0])) instanceof HCFPlayer) {
				$sender->sendMessage(TextFormat::GREEN . $player->getName() . "'s current balance " . TextFormat::GOLD . $player->getBalance());
			} else {
				$sender->sendMessage(TextFormat::RED . "Player in not online!");
			}
		}
	}
}