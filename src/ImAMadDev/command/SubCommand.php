<?php

namespace ImAMadDev\command;

use JetBrains\PhpStorm\Pure;
use pocketmine\Server;
use pocketmine\command\CommandSender;

use ImAMadDev\HCF;

abstract class SubCommand {


    /**
     * @param string $name
     * @param string|null $usage
     * @param array $aliases
     * @param string $permission
     */
    public function __construct(
        private string $name,
        private ?string $usage = null,
        private array $aliases = [],
        private string $permission = ""){}
	
	#[Pure] public function getMain(): HCF {
		return HCF::getInstance();
	}
	
	public function getServer() : Server {
		return Server::getInstance();
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function getUsage(): ?string {
		return $this->usage;
	}
	
	public function getAliases(): array {
		return $this->aliases;
	}

    /**
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

    public function canExecute(CommandSender $sender) : bool
    {
        if ($this->getPermission() == "") return true;
        return $sender->hasPermission($this->getPermission());
    }
	
	abstract public function execute(CommandSender $sender, string $commandLabel, array $args): void;
}