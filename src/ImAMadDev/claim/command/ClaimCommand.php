<?php

namespace ImAMadDev\claim\command;

use ImAMadDev\command\Command;
use ImAMadDev\claim\command\subCommands\CreateSubCommand;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ClaimCommand extends Command {
	
	public function __construct() {
		parent::__construct("claim", "Manage claim", "/claim help <1-5>", ["opclaim"]);
		$this->addSubCommand(new CreateSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("opclaim.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(isset($args[0])) {
			$subCommand = $this->getSubCommand($args[0]);
			if($subCommand !== null) {
				$subCommand->execute($sender, $commandLabel, $args);
				return;
			}
			$sender->sendMessage("/claim help <1-5>");
			return;
		}
		$sender->sendMessage("/claim help <1-5>");
	}
}