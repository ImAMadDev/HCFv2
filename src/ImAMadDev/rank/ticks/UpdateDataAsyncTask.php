<?php

namespace ImAMadDev\rank\ticks;

use ImAMadDev\HCF;
use pocketmine\scheduler\AsyncTask;
use ImAMadDev\rank\RankClass;

class UpdateDataAsyncTask extends AsyncTask {
	
	public function __construct(
        public mixed $key,
        public mixed $value,
        public string $rank,
        public bool $nested){}
	
	public function onRun() : void {
	}
	
	public function onCompletion() : void {
		$main = HCF::getInstance();
		if(($rank = $main::$rankManager->getRank($this->rank)) instanceof RankClass) {
			$rank->updateData($this->key, $this->value, $this->nested);
			$main->getLogger()->info("Datos Actualizados");
		}
	}

}