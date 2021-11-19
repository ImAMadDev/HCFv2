<?php

namespace ImAMadDev\manager;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\events\{Events, EventsCreator};

use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class EventsManager{
    use SingletonTrait;
	
	private static array $events = [];
	public static ? HCF $main = null;

	public static array $creators = [];
	
	public function __construct(HCF $main){
		self::$main = $main;
        self::setInstance($this);
	}
	
	public function addCreator(HCFPlayer $player, string $event): string{
		if(isset(self::$creators[$player->getName()])) return TextFormat::RED . "Ya estas en creacion";
		self::$creators[$player->getName()] = new EventsCreator($player, $event);
		return TextFormat::GREEN . "Haz sido agregado a la creacion de eventos con el evento: ".$event;
	}
	
	public function creatorExists(HCFPlayer $player): bool{
		if(isset(self::$creators[$player->getName()])) return true;
		return false;
	}
	
	public function addCommand(string $command, HCFPlayer $player): string{
		return self::$creators[$player->getName()]->addCommand($command);
	}
	
	public function setTime(int $time, HCFPlayer $player): string{
		return self::$creators[$player->getName()]->setTime($time);
	}
	
	public function setScoreboard(string $tag, HCFPlayer $player): string{
		return self::$creators[$player->getName()]->setScoreboard($tag);
	}
	
	public function addEvent(HCFPlayer $player): bool{
		$class = self::$creators[$player->getName()];
		self::$events[$class->name] = new Events(["name" => $class->name, "commands" => $class->commands, "time" => $class->time, "scoreboard" => $class->scoreboard], $this);
		unset(self::$creators[$player->getName()]);
		return true;
	}
	
	public function eventExists(string $name): bool{
		return isset(self::$events[$name]);
	}
	
	public function getEvent(string $name): ? Events{
		if(!isset(self::$events[$name])) return null;
		return self::$events[$name];
	}
	
	public function removeEvent(string $name): bool{
		if(!isset(self::$events[$name])) return true;
		$this->getEvent($name)->disable();
		unset(self::$events[$name]);
		return true;
	}
	
	public function getEvents(): array{
		return self::$events;
	}
	
	public function getEventsList() : string {
		return TextFormat::GREEN . "Events loaded: " . implode(", ", array_keys(self::$events));
	}
	
}