<?php

namespace ImAMadDev\rank\ticks;

use ImAMadDev\HCF;
use pocketmine\scheduler\AsyncTask;
use ImAMadDev\rank\RankClass;

class UpdateDataAsyncTask extends AsyncTask {
	
	public function __construct(
        public string $rank){}
	
	public function onRun() : void {
	}
	
	public function onCompletion() : void {
		$main = HCF::getInstance();
		if(($rank = $main::$rankManager->getRank($this->rank)) instanceof RankClass) {
			$rank->updateData();
		}
	}

}