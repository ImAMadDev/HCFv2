<?php

namespace ImAMadDev\ability\ticks;

use ImAMadDev\player\HCFPlayer;

use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class StormBreakerTick extends Task {
	
	protected ?HCFPlayer $player = null;
	
	protected ? Item $item = null;
	
	public int $time = 8;
	
	public function __construct(HCFPlayer $player){
		$this->player = $player;
		$this->item = clone $player->getArmorInventory()->getHelmet();
		$player->getArmorInventory()->setHelmet(ItemFactory::air());
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
			$player->getArmorInventory()->setHelmet($this->item);
			$this->getHandler()->cancel();
		} else {
			$player->sendTip(TextFormat::RED . 'Returning helmet in: ' . $this->time);
		}
	}
}