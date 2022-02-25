<?php

namespace ImAMadDev\ticks\player;


use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;

use pocketmine\color\Color;
use pocketmine\math\Vector3;
use pocketmine\world\particle\DustParticle;
use pocketmine\scheduler\Task;

class ParticleTick extends Task {
	
	public HCFPlayer $player;
	private int $r = 0;

	public function __construct(HCFPlayer $player) {
		$this->player = $player;
		HCF::getInstance()->getScheduler()->scheduleRepeatingTask($this, 3);
	}

	public function onRun() : void {
		$player = $this->player;
		if(!$player->isOnline()){
			$this->getHandler()->cancel();
			return;
		}
		if(!$player->hasPermission('vip.particle')){
			$player->particleTick = null;
            $this->getHandler()->cancel();
			return;
		}
		if($player->hasParticle()) {
			$size = 0.6;
			$a = cos(deg2rad($this->r/0.06))* $size;
			$b = sin(deg2rad($this->r/0.06))* $size;
			$player->getWorld()->addParticle(new Vector3($player->getPosition()->x - $a, $player->getPosition()->y + 2, $player->getPosition()->z - $b), new DustParticle(new Color(mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255))));
			$player->getWorld()->addParticle(new Vector3($player->getPosition()->x + $a, $player->getPosition()->y + 2, $player->getPosition()->z + $b), new DustParticle(new Color(mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255))));
			$this->r++;
		} else {
			$player->particleTick = null;
			$this->getHandler()->cancel();
		}
	}
}