<?php

namespace ImAMadDev\koth\ticks;

use pocketmine\scheduler\Task;

use ImAMadDev\manager\KOTHManager;

class KOTHTick extends Task {
	
	private KOTHManager $manager;
	
	public function __construct(KOTHManager $manager) {
		$this->manager = $manager;
	}
	
	public function onRun(): void{
        $this->manager->getSelected()?->onTick();
	}
	
}