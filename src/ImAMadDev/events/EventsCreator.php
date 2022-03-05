<?php

namespace ImAMadDev\events;

use ImAMadDev\player\HCFPlayer;

use pocketmine\utils\TextFormat;

class EventsCreator{
	
	public ? HCFPlayer $player = null;
	
	public string $name;
	
	public array $commands = [];
	
	public string $scoreboard;
	
	public int $time = 300;
	
	public function __construct(HCFPlayer $player, string $arena){
		$this->player = $player;
		$this->name = $arena;
	}
	
	public function addCommand(string $command): string{
		$this->commands[] = $command;
		return TextFormat::GREEN . "Has agregado el comando: " . $command;
	}
	
	public function setTime(int $time): string{
		$this->time = $time;
		return TextFormat::GREEN . "Has puesto el tiempo a: " .gmdate("H:i:s", $this->time);
	}
	
	public function setScoreboard(string $scoreboard): string{
		$this->scoreboard = $scoreboard;
		return TextFormat::GREEN . "Has puesto el formato de la Scoreboard a: " . $scoreboard;
	}
}
