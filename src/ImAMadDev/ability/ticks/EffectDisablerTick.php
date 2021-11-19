<?php

namespace ImAMadDev\ability\ticks;

use ImAMadDev\player\HCFPlayer;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class EffectDisablerTick extends Task {

    /**
     * @var HCFPlayer|null $player
     */
    protected HCFPlayer|null $player = null;

    /**
     * @var int $time
     */
    public int $time = 15;

    /**
     * @param HCFPlayer $player
     */
    public function __construct(HCFPlayer $player){
		$this->player = $player;
		$player->activateEffects(false);
		$player->getEffects()->clear();
	}

    public function onRun() : void {
		$player = $this->player;
		if(!$player->isOnline()){
			$this->getHandler()->cancel();
			return;
		}
		if(!$player->isAlive()) {
            $this->getHandler()->cancel();
            return;
		}
		if($this->time-- <= 0) {
			$player->activateEffects(true);
			$this->getHandler()->cancel();
		} else {
			$player->sendTip(TextFormat::GOLD . 'Returning effects in: ' . $this->time);
		}
	}
}