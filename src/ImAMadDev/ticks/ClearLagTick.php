<?php

namespace ImAMadDev\ticks;

use pocketmine\entity\object\ItemEntity;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\entity\projectile\Arrow;
use pocketmine\utils\TextFormat;

use ImAMadDev\HCF;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\entity\mobs\{Enderman, Creeper, Blaze, Cow};
use ImAMadDev\entity\CombatLogger;

class ClearLagTick extends Task{
	
	protected int $time = 300;
	
	public function __construct(){
		$this->time = HCFUtils::CLEARLAG_TIME;
	}
	
	public function onRun() : void {
		if(in_array($this->time, [30, 10, 5])){
			$time = $this->time . " &csecond(s)&r";
			Server::getInstance()->broadcastMessage(TextFormat::colorize("&7All entities will be cleared from the ground in: &c" . $time));
		}
		if($this->time <= 0){
			$this->clearItems();
			$this->time = HCFUtils::CLEARLAG_TIME;
		} else {
			$this->time--;
		}
	}
	
	public function clearItems(): void{
		$count = 0;
		foreach(Server::getInstance()->getWorldManager()->getWorlds() as $lvl){
			foreach($lvl->getEntities() as $en){
				if($en instanceof ItemEntity || $en instanceof Blaze || $en instanceof CombatLogger || $en instanceof Arrow || $en instanceof Enderman || $en instanceof Cow || $en instanceof Creeper){
					$count += 1;
					$en->flagForDespawn();
				}
			}
		}
		Server::getInstance()->broadcastMessage(TextFormat::colorize("&cA total of&7 [&l&6 ". $count." &r&7] &r&cDropped items were removed.&r"));
	}

}
