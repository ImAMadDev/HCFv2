<?php

namespace ImAMadDev\ticks\events;

use ImAMadDev\HCF;
use ImAMadDev\manager\PurgeManager;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class PurgeTick extends Task {

	private static HCF $main;

	public function __construct(HCF $main) {
		self::$main = $main;
		$main->getScheduler()->scheduleRepeatingTask($this, 20);
	}

	public function onRun() : void {
		if(PurgeManager::isEnabled()) {
			if(PurgeManager::getTime() <= 0) {
				PurgeManager::set(false);
				self::$main->getServer()->broadcastMessage(TextFormat::RED . "Purge was ended!");
				$this->getHandler()->cancel();
			} else {
				PurgeManager::reduceTime();
			}
		}
	}
}