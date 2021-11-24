<?php

namespace ImAMadDev\npc\command;

use ImAMadDev\command\Command;
use ImAMadDev\npc\command\subCommands\SpawnSubCommand;
use ImAMadDev\npc\command\subCommands\DespawnSubCommand;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class NPCCommand extends Command {
	
	public function __construct() {
		parent::__construct("npc", "Manage NPC", "/npc help ");
		$this->addSubCommand(new SpawnSubCommand());
		$this->addSubCommand(new DespawnSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("npc.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(isset($args[0])) {
			$subCommand = $this->getSubCommand($args[0]);
			if($subCommand !== null) {
				$subCommand->execute($sender, $commandLabel, $args);
				return;
			}
			$sender->sendMessage($this->getUsage());
			return;
		}
		$sender->sendMessage($this->getUsage());
	}
}