<?php

namespace ImAMadDev\koth\ticks;

use pocketmine\scheduler\Task;
use ImAMadDev\HCF;

class AutomaticKOTHTick extends Task {
	
	public ?HCF $plugin = null;
	
	public function __construct(HCF $plugin, int $time) {
		$this->plugin = $plugin;
		$plugin->getScheduler()->scheduleDelayedTask($this, $time);
	}
	
	public function getPlugin() :? HCF {
		return $this->plugin;
	}
	
	public function getRandomArena() : ? string {
		$arenas = $this->getPlugin()->getKOTHManager()->getArenas();
		$arena = $arenas[array_rand($arenas)];
		if(!is_null($arena)) {
			return $arena->getName();
		} else {
			return null;
		}
	}
	public function onRun() : void {
		if(count($this->getPlugin()->getKOTHManager()->getArenas()) >= 1) {
			$this->getPlugin()->getKOTHManager()->selectArena($this->getRandomArena());
		}
	}

}
