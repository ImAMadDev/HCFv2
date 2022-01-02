<?php

namespace ImAMadDev\command;

use JetBrains\PhpStorm\Pure;
use pocketmine\Server;
use pocketmine\command\CommandSender;

use ImAMadDev\HCF;

abstract class Command extends \pocketmine\command\Command {

    /**
     * @var SubCommand[]
     */
    private array $subCommands;
	
	#[Pure] public function getMain(): HCF {
		return HCF::getInstance();
	}
	
	public function getServer() : Server {
		return Server::getInstance();
	}

    public function canExecute(CommandSender $sender) : bool
    {
        if ($this->getPermission() == "") return true;
        return $sender->hasPermission($this->getPermission());
    }
	
	public function addSubCommand(SubCommand $subCommand): void {
		$this->subCommands[$subCommand->getName()] = $subCommand;
		foreach($subCommand->getAliases() as $alias) {
			$this->subCommands[$alias] = $subCommand;
		}
	}
	
	public function getSubCommand(string $name): ?SubCommand {
		return $this->subCommands[$name] ?? null;
	}
	
	abstract public function execute(CommandSender $sender, string $commandLabel, array $args): void;
}