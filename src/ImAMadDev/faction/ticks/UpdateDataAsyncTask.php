<?php

namespace ImAMadDev\faction\ticks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

use ImAMadDev\faction\Faction;

class UpdateDataAsyncTask extends AsyncTask {
	

	public function __construct(
        public string $faction){}
	
	public function onRun() : void {
	}
	
	public function onCompletion() : void {
		$main = Server::getInstance()->getPluginManager()->getPlugin("HCF");
		if(($faction = $main::$factionManager->getFaction($this->faction)) instanceof Faction) {
			$faction->updateData();
		}
	}

}