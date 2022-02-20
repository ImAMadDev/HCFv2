<?php

namespace ImAMadDev\manager;

use pocketmine\player\GameMode;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerDeathEvent, PlayerRespawnEvent, PlayerPreLoginEvent};
use ImAMadDev\HCF;
use ImAMadDev\ticks\events\PurgeTick;

class PurgeManager implements Listener {
	
	public static HCF $main;
	public static bool $enabled = false;
	public static int $time = 3600;
	public static ?PurgeTick $tick = null;
	
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
				self::$tick = new PurgeTick(self::$main);
			}
		} else {
			self::$tick->getHandler()->cancel();
			self::$tick = null;
		}
	}
	
	public static function getTime() : int {
		return self::$time;
	}
	
	public static function reduceTime() : void {
		self::$time -= 1;
	}
}