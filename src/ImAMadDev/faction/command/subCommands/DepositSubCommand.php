<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DepositSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("deposit", "/faction deposit <amount>", ["d"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if($sender->getFaction() === null) {
			$sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if($args[1] === "all") {
			$amount = $sender->getBalance();
		} else {
			$amount = intval($args[1]);
		}
		if(!is_numeric($amount)) {
			$sender->sendMessage(TextFormat::RED . "The number you've entered isn't a valid number!");
			return;
		}
		$amount = max(0, $amount);
		if($sender->getBalance() < $amount) {
			$sender->sendMessage(TextFormat::RED . "You don't have enough money!");
			return;
		}
		$sender->reduceBalance($amount);
		$sender->getFaction()->addBalance($amount);
		$sender->getFaction()->message(TextFormat::GREEN . $sender->getName() . TextFormat::YELLOW . " has deposited " . TextFormat::LIGHT_PURPLE . $amount);
	}
}