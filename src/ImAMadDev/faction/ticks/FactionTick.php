<?php

namespace ImAMadDev\faction\ticks;

use ImAMadDev\manager\FactionManager;
use pocketmine\scheduler\Task;

use ImAMadDev\faction\{FactionUtils, Faction};

class FactionTick extends Task {
	
	private Faction $faction;

	public function __construct(Faction $faction) {
		$this->faction = $faction;
	}
	
	public function onRun() : void {
		$faction = $this->faction;
		if(!FactionManager::getInstance()->isFaction($faction)) {
			$this->getHandler()->cancel();
			return;
		}
		if($faction->getDTR() < $faction->getMaxDTR()) {
			if($faction->freezeTime-- <= 0){
				if($faction->regenerationTime-- <= 0){
					$faction->addDTR(FactionUtils::DTR_TO_ADD);
					$faction->regenerationTime = FactionUtils::REGENERATION_TIME;
				}
			}
		} else {
			$this->getHandler()->cancel();
		}
	}
}