<?php

namespace ImAMadDev\koth\command;

use ImAMadDev\command\Command;
use ImAMadDev\koth\command\subCommands\SetKeysSubCommand;
use ImAMadDev\koth\command\subCommands\SetCornerSubCommand;
use ImAMadDev\koth\command\subCommands\SetPosSubCommand;
use ImAMadDev\koth\command\subCommands\SetTimeSubCommand;
use ImAMadDev\koth\command\subCommands\CreateSubCommand;
use ImAMadDev\koth\command\subCommands\DisbandSubCommand;
use ImAMadDev\koth\command\subCommands\SaveSubCommand;
use ImAMadDev\koth\command\subCommands\EnableSubCommand;
use ImAMadDev\koth\command\subCommands\InfoSubCommand;
use ImAMadDev\koth\command\subCommands\ListSubCommand;
use ImAMadDev\koth\command\subCommands\DisableSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class KOTHCommand extends Command {
	
	public function __construct() {
		parent::__construct("koth", "Manage koth", "/koth help <1-5>");
		$this->addSubCommand(new ListSubCommand());
		$this->addSubCommand(new DisbandSubCommand());
		$this->addSubCommand(new CreateSubCommand());
		$this->addSubCommand(new InfoSubCommand());
		$this->addSubCommand(new SetTimeSubCommand());
		$this->addSubCommand(new SetKeysSubCommand());
		$this->addSubCommand(new SetCornerSubCommand());
		$this->addSubCommand(new SaveSubCommand());
		$this->addSubCommand(new SetPosSubCommand());
		$this->addSubCommand(new EnableSubCommand());
		$this->addSubCommand(new DisableSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(isset($args[0])) {
			$subCommand = $this->getSubCommand($args[0]);
			if($subCommand !== null) {
				$subCommand->execute($sender, $commandLabel, $args);
				return;
			}
			$sender->sendMessage("/koth help <1-5>");
			return;
		}
		$sender->sendMessage("/koth help <1-5>");
    }
}
