<?php

namespace ImAMadDev\koth\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;

class ListSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("list", "/koth list");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		$sender->sendMessage($this->getMain()->getKOTHManager()->getKoths());
	}
}