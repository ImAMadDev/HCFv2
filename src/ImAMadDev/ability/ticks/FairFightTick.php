<?php

namespace ImAMadDev\ability\ticks;

use ImAMadDev\player\HCFPlayer;

use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class FairFightTick extends Task {
	
	protected ?HCFPlayer $player = null;
	
	protected ?Item $helmet = null, $chestplate = null, $leggings = null, $boots = null;
	
	public int $time = 8;
	
	public function __construct(HCFPlayer $player){
		$this->player = $player;
		$this->helmet = clone $player->getArmorInventory()->getHelmet();
		$this->chestplate = clone $player->getArmorInventory()->getChestplate();
		$this->leggings = clone $player->getArmorInventory()->getLeggings();
		$this->boots = clone $player->getArmorInventory()->getBoots();
		$helmet = clone $player->getArmorInventory()->getHelmet();
		$chestplate = clone $player->getArmorInventory()->getChestplate();
		$leggings = clone $player->getArmorInventory()->getLeggings();
		$boots = clone $player->getArmorInventory()->getBoots();
		$player->getArmorInventory()->setHelmet($helmet->addEnchantment(new );
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