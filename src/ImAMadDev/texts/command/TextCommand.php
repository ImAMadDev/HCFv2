<?php

namespace ImAMadDev\texts\command;

use ImAMadDev\command\Command;
use ImAMadDev\texts\command\subCommands\CreateSubCommand;
use ImAMadDev\texts\command\subCommands\ListSubCommand;
use ImAMadDev\texts\command\subCommands\DisbandSubCommand;
use ImAMadDev\texts\command\subCommands\EditSubCommand;
use pocketmine\command\CommandSender;

class TextCommand extends Command {
	
	public function __construct() {
		parent::__construct("text", "Manage text", "/text help <1-5>");
		$this->addSubCommand(new CreateSubCommand());
		$this->addSubCommand(new ListSubCommand());
		$this->addSubCommand(new DisbandSubCommand());
		$this->addSubCommand(new EditSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("texts.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(isset($args[0])) {
			$subCommand = $this->getSubCommand($args[0]);
			if($subCommand !== null) {
				$subCommand->execute($sender, $commandLabel, $args);
				return;
			}
			$sender->sendMessage("/text help <1-5>");
			return;
		}
		$sender->sendMessage("/text help <1-5>");
	}
}
?>