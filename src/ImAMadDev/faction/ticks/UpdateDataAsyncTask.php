<?php

namespace ImAMadDev\faction\ticks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

use ImAMadDev\faction\Faction;

class UpdateDataAsyncTask extends AsyncTask {
	
	private string $faction;
	private mixed $key;
	private mixed $value;
	private bool $nested;
	
	public function __construct(mixed $key,  mixed $value, string $faction, bool $nested){
		$this->key = $key;
		$this->value = $value;
		$this->faction = $faction;
		$this->nested = $nested;
	}
	
	public function onRun() : void {
	}
	
	public function onCompletion() : void {
		$main = Server::getInstance()->getPluginManager()->getPlugin("HCF");
		if(($faction = $main::$factionManager->getFaction($this->faction)) instanceof Faction) {
			$faction->updateData($this->key, $this->value, $this->nested);
			$main->getLogger()->info("Datos Actualizados");
		}
	}

}