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
     */
    public function __construct(
        private string $name,
        private ?string $usage = null,
        private array $aliases = []){}
	
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
	
	abstract public function execute(CommandSender $sender, string $commandLabel, array $args): void;
}