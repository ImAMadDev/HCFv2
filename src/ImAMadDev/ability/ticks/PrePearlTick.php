<?php

namespace ImAMadDev\ability\ticks;

use ImAMadDev\player\HCFPlayer;

use pocketmine\world\Position;

use pocketmine\scheduler\Task;

class PrePearlTick extends Task {
	
	private HCFPlayer  $player;
	
	private Position $usePosition;
	
	public function __construct(HCFPlayer $player) {
		$this->player = $player;
		$this->usePosition = $player->getPosition();
	}
	
	public function onRun() : void {
		$player = $this->player;
		if(!$player->isOnline()){
            $this->getHandler()->cancel();
            return;
    	}
    	$player->teleport($this->usePosition);
    	$this->getHandler()->cancel();
    }
}