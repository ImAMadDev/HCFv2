<?php

namespace ImAMadDev\ticks\player;

use ImAMadDev\faction\Faction;
use ImAMadDev\player\PlayerCache;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class UpdateDataAsyncTask extends AsyncTask {

    private int $time;

	public function __construct(){
        $this->time = microtime(true);
        Server::getInstance()->broadcastMessage(TextFormat::DARK_GREEN . "Saving data...");
    }
	
	public function onRun() : void {
	}
	
	public function onCompletion() : void {
		$main = Server::getInstance()->getPluginManager()->getPlugin("HCF");
        $time = microtime(true) - $this->time;
        foreach ($main::$instance->getPlayerCache() as $cache) {
            if ($cache instanceof  PlayerCache) $cache->saveData();
        }
        foreach ($main::$factionManager->getFactions() as $faction) {
            if ($faction instanceof Faction) $faction->saveData();
        }
        Server::getInstance()->broadcastMessage(TextFormat::GREEN . "Data saved in: " . round($time, 3) . "ms!");
	}

}