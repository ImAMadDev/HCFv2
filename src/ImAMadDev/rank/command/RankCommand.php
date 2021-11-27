<?php

namespace ImAMadDev\rank\command;

use ImAMadDev\command\Command;
use ImAMadDev\rank\command\subCommands\CreateSubCommand;
use ImAMadDev\rank\command\subCommands\GetSubCommand;
use ImAMadDev\rank\command\subCommands\ListSubCommand;
use ImAMadDev\rank\command\subCommands\AddPermissionSubCommand;
use ImAMadDev\rank\command\subCommands\InfoSubCommand;
use ImAMadDev\rank\command\subCommands\GiveSubCommand;
use ImAMadDev\rank\command\subCommands\RemoveSubCommand;
use ImAMadDev\rank\command\subCommands\SetReclaimSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RankCommand extends Command {
	
	public function __construct() {
		parent::__construct("rank", "Manage rank", "/rank help <1-5>", ["r"]);
		$this->addSubCommand(new CreateSubCommand());
		$this->addSubCommand(new AddPermissionSubCommand());
		$this->addSubCommand(new InfoSubCommand());
		$this->addSubCommand(new GiveSubCommand());
		$this->addSubCommand(new ListSubCommand());
		$this->addSubCommand(new RemoveSubCommand());
        $this->addSubCommand(new GetSubCommand());
        $this->addSubCommand(new SetReclaimSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("rank.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(isset($args[0])) {
			$subCommand = $this->getSubCommand($args[0]);
			if($subCommand !== null) {
				$subCommand->execute($sender, $commandLabel, $args);
				return;
			}
			$sender->sendMessage("/rank help <1-5>");
			return;
		}
		$sender->sendMessage("/rank help <1-5>");
		return;
	}
}