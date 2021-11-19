<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\faction\ticks\StuckTask;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class StuckSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("stuck", "/faction stuck");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage("noPermission");
			return;
		}
		if($sender->getCooldown()->has('combattag')) {
			$sender->sendMessage(TextFormat::RED . "You're combat tagged!");
			return;
		}
		if($sender->getCooldown()->has('home_teleport')) {
			$sender->sendMessage(TextFormat::RED . "Already in home cooldown!");
			return;
		}
		if($sender->getCooldown()->has('stuck_teleport')) {
			$sender->sendMessage(TextFormat::RED . "Already in stuck cooldown!");
			return;
		}
		if($sender->getCooldown()->has('logout')) {
			$sender->sendMessage(TextFormat::RED . "Already in logout cooldown!");
			return;
		}
		$sender->getCooldown()->add('stuck_teleport', 40);
		new StuckTask($sender);
	}
}