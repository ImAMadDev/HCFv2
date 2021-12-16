<?php

namespace ImAMadDev\ticks\events;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\SOTWManager;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class StartOfTheWorldTick extends Task {

	private static HCF $main;

	public function __construct(HCF $main) {
		self::$main = $main;
		$main->getScheduler()->scheduleRepeatingTask($this, 20);
	}

	public function onRun() : void {
		if(SOTWManager::isEnabled()) {
			if(SOTWManager::getTime() <= 0) {
				SOTWManager::set(false);
				self::$main->getServer()->broadcastMessage(TextFormat::RED . "Start Of The World was ended!");
                $this->showAll();
				$this->getHandler()->cancel();
			} else {
                $this->reduceLag();
				SOTWManager::reduceTime();
			}
		}
	}


    private function reduceLag(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if ($player instanceof HCFPlayer){
                if(stripos($player->getRegion()->get(), "Spawn") !== false){
                    foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                        $onlinePlayer->hidePlayer($player);
                    }
                } else {
                    foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                        $onlinePlayer->showPlayer($player);
                    }
                }
            }
        }
    }

    public function showAll()
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                $onlinePlayer->showPlayer($player);
            }
        }
    }

}