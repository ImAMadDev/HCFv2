<?php

namespace ImAMadDev\events;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

use ImAMadDev\HCF;
use ImAMadDev\manager\EventsManager;
use ImAMadDev\events\ticks\EventCooldownTick;
use ImAMadDev\utils\DiscordIntegration;

class Events{
	
	public string $name;
	
	public array $commands = [];
	
	public string $scoreboard;
	
	public int $time = 300;
	
	public ?EventCooldownTick $task = null;
	
	public ?EventsManager $manager = null;
	
	public function __construct(array $data, EventsManager $manager) {
		$this->name = $data["name"];
		$this->commands = $data["commands"];
		$this->time = $data["time"];
		$this->scoreboard = $data["scoreboard"];
		$this->manager = $manager;
		HCF::getInstance()->getScheduler()->scheduleRepeatingTask($this->task = new EventCooldownTick($this), 20);
		$time = date('l jS \of F Y h:i:s A', (time() + $this->time));
		$fields = ["   " . strtoupper($this->name), "The event will take place on $time  :clock3:", "IP: play.minestalia.com", "SHOP: minestalia.com", "━━━━━━━━ ⇜ ━━━━━━━━"];
		DiscordIntegration::sendToDiscord("━━━━━━━━ ⇜ ━━━━━━━━", "<@&869250299087421491>", DiscordIntegration::KOTH_WEBHOOK, "StaliaBot", $fields);
	}
	
	public function getScoreboard(): string{
		return TextFormat::colorize($this->scoreboard);
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function getServer(): Server{
		return Server::getInstance();
	}
	
	public function getCommands() : array {
		return $this->commands;
	}
	
	public function getTime(): int {
		return $this->time;
	}
	
	public function initialize(): void{
		$fields = ["_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _  EVENT {$this->getName()}", "_ _ _ _ _ _ _ _ _ _ _ _ _ _ Event has been triggered :clock3:", "━━━━━━━━ ⇜ ━━━━━━━━", "IP: play.minestalia.com", "PORT: 19132", "SHOP: minestalia.com"];
		DiscordIntegration::sendToDiscord("━━━━━━━━ ⇜ ━━━━━━━━", "<@&869250299087421491>", DiscordIntegration::KOTH_WEBHOOK, "StaliaBot", $fields);
		$this->getServer()->broadcastMessage(TextFormat::GREEN . "A new event has been triggered: " . TextFormat::RED . $this->getName());
		foreach($this->getCommands() as $command) {
			$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), str_replace("/", "", $command));
		}
		$this->finish();
	}
	
	public function disable() : void {
		$this->task->getHandler()->cancel();
	}
	
	public function finish() : void {
		$this->manager->removeEvent($this->getName());
		$this->task->getHandler()->cancel();
	}
	
	public function onTick(): void{
		if($this->time-- <= 0) {
			$this->initialize();
		}
	}

}