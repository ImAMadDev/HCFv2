<?php

namespace ImAMadDev\events\command;

use ImAMadDev\command\Command;
use ImAMadDev\events\command\subCommands\AddCommandSubCommand;
use ImAMadDev\events\command\subCommands\SetScoreboardSubCommand;
use ImAMadDev\events\command\subCommands\SetTimeSubCommand;
use ImAMadDev\events\command\subCommands\CreateSubCommand;
use ImAMadDev\events\command\subCommands\SaveSubCommand;
use ImAMadDev\events\command\subCommands\ListSubCommand;
use ImAMadDev\events\command\subCommands\DisableSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class EventCommand extends Command {
	
	public function __construct() {
		parent::__construct("event", "Manage Events", "/event help <1-5>");
		$this->addSubCommand(new ListSubCommand());
		$this->addSubCommand(new CreateSubCommand());
		$this->addSubCommand(new SetTimeSubCommand());
		$this->addSubCommand(new SetScoreboardSubCommand());
		$this->addSubCommand(new AddCommandSubCommand());
		$this->addSubCommand(new SaveSubCommand());
		$this->addSubCommand(new DisableSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("event.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(isset($args[0])) {
			$subCommand = $this->getSubCommand($args[0]);
			if($subCommand !== null) {
				$subCommand->execute($sender, $commandLabel, $args);
				return;
			}
			$sender->sendMessage("/event help <1-5>");
			return;
		}
		$sender->sendMessage("/event help <1-5>");
		return;
	}
}
?>