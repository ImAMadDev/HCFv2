<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class WithdrawSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("withdraw", "/faction withdraw <amount>", ["w"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if($sender->getFaction() === null) {
			$sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
			return;
		}
		if(!$sender->getFaction()->isLeader($sender->getName()) || !$sender->getFaction()->isColeader($sender->getName())) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$amount = (int)$args[1];
		if(!is_numeric($amount)) {
			$sender->sendMessage(TextFormat::RED . "The number you've entered isn't a valid number!");
			return;
		}
		$amount = max(0, $amount);
		if($sender->getFaction()->getBalance() < $amount) {
			$sender->sendMessage(TextFormat::RED . "Your faction don't have enough money!");
			return;
		}
		$sender->getFaction()->removeBalance($amount);
		$sender->addBalance($amount);
		$sender->getFaction()->message(TextFormat::GREEN . $sender->getName() . TextFormat::GRAY . " has withdrew $" . TextFormat::LIGHT_PURPLE . $amount);
	}
}