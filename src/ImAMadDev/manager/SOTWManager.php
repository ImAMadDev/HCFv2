<?php

namespace ImAMadDev\manager;

use pocketmine\utils\{TextFormat, Config};
use pocketmine\event\Listener;
use pocketmine\event\entity\{EntityDamageEvent};
use ImAMadDev\HCF;
use ImAMadDev\faction\Faction;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\ticks\events\StartOfTheWorldTick;

class SOTWManager implements Listener {
	
	public static HCF $main;
	public static bool $enabled = false;
	public static int $time = 3600;
	public static ?StartOfTheWorldTick $tick = null;
	
	public function __construct(HCF $main) {
		self::$main = $main;
	}
	
	public static function isEnabled() : bool {
		return self::$enabled;
	}
	
	public static function set(bool $status = false, int $time = 60): void {
		self::$enabled = $status;
		self::$time = ($time * 60);
		if($status === true) {
			if(self::$tick === null) {
				self::$tick = new StartOfTheWorldTick(self::$main);
			}
		} else {
			self::$tick->getHandler()->cancel();
			self::$tick = null;
		}
	}
	
	public static function getTime() : int
    {
		return self::$time;
	}

    public static function setTime(int $time) : void
    {
        self::$time = ($time * 60);
    }
	
	public static function reduceTime() : void {
		self::$time -= 1;
	}
	
	public function onEntityDamageEvent(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($player instanceof HCFPlayer) { 
			if(self::isEnabled() === true) {
				$event->cancel();
			}
		}
	}
	
}