<?php

namespace ImAMadDev\item;

use pocketmine\level\sound\LaunchSound;

class FishingRod extends \pocketmine\item\ProjectileItem {
	
	public function __construct($meta = 0){
		parent::__construct(\pocketmine\item\Item::FISHING_ROD, $meta, "Fishing Rod");
	}
	
	public function onClickAir(\pocketmine\Player $player, \pocketmine\math\Vector3 $directionVector) : bool {
		return true;
	}
	
	public function getMaxStackSize() : int {
		return 1;
	}
	
	public function getProjectileEntityType() : string {
		return "FishingHook";
	}
	
	public function getThrowForce() : float {
        return 2.1;
	}
}