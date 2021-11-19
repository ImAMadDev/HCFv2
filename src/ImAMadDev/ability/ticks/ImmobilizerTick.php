<?php

namespace ImAMadDev\ability\ticks;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;

use pocketmine\scheduler\Task;

class ImmobilizerTick extends Task {
	
	private HCFPlayer  $player;
	
	public function __construct(HCFPlayer $player) {
		$this->player = $player;
		$player->setImmobile(true);
	}
	
	public function onRun() : void {
		$player = $this->player;
		if(!$player->isOnline()){
            $this->getHandler()->cancel();
            return;
    	}
    	$player->setImmobile(false);
    	$this->getHandler()->cancel();
    }
}
