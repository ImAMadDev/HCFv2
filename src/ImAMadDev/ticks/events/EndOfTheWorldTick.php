<?php

namespace ImAMadDev\ticks\events;

use ImAMadDev\HCF;
use ImAMadDev\manager\EOTWManager;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class EndOfTheWorldTick extends Task {

	private static HCF $main;

	public function __construct(HCF $main) {
		self::$main = $main;
		$main->getScheduler()->scheduleRepeatingTask($this, 20);
		$this->unClaim();
	}

	public function onRun() : void {
		if(EOTWManager::isEnabled()) {
			if(EOTWManager::getTime() === 120) {
				self::$main->getServer()->broadcastMessage(HCF::$factionManager->getTopPower());
			}
			if(EOTWManager::getTime() <= 0) {
				EOTWManager::set(false);
				self::$main->getServer()->broadcastMessage(TextFormat::RED . "Start Of The World was ended!");
				$this->getHandler()->cancel();
			} else {
				EOTWManager::reduceTime();
			}
		}
	}
	
	public function unClaim() : void {
		foreach(HCF::$factionManager->getFactions() as $faction) {
			if(!$faction->hasClaim()) {
				continue;
			}
			$faction->unClaim();
		}
		self::$main->getServer()->broadcastMessage(TextFormat::RED . "All faction was unclaimed!");
	}

}